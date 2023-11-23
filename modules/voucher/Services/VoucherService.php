<?php


namespace Voucher\Services;

use Carbon\Carbon;
use Discount\Services\DiscountService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Factories\ServicesFactory;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Repositories\Collections\ServiceRepository;
use Voucher\Exceptions\VoucherExceptions;

class VoucherService
{
    protected $saleRepository;
    protected $discountService;

    public function __construct(SaleRepository $saleRepository, DiscountService $discountService)
    {
        $this->saleRepository  = $saleRepository;
        $this->discountService = $discountService;
    }

    public function useDiscount(array $requestData): Service
    {
        $transactionId = data_get($requestData, 'transactionId');
        $imei          = data_get($requestData, 'imei');
        $metadata      = data_get($requestData, 'metadata');

        $service = $this->getServiceByTransaction($transactionId);
        $this->validateVoucher($service, $imei);
        return $this->burnVoucher($service, $imei, $metadata);
    }

    public function getServiceByTransaction(string $serviceTransactionId): ?Service
    {
        $service = $this->saleRepository->findInSale($serviceTransactionId);

        throw_if($service === null, VoucherExceptions::NotFound());

        return $service;
    }

    public function validateVoucher(Service $service, string $newImei = null): bool
    {
        $sale = $service->sale;

        $user = Auth::user();
        throw_if(
            $sale->pointOfSale['network']['id'] !== $user->getNetwork()->id,
            VoucherExceptions::VoucherNotBelongsToNetwork()
        );

        throw_if(
            $service->burned !== null && ! empty($service->burned['current']),
            VoucherExceptions::VoucherBurned()
        );

        throw_if(
            $newImei !== null &&
            $service->status === ServiceStatus::ACCEPTED &&
            $service->sector === Operations::TELECOMMUNICATION &&
            $newImei !== $service->imei,
            VoucherExceptions::VoucherTelecommunicationDifferentImei()
        );

        throw_if(
            (! in_array($service->status, [ServiceStatus::APPROVED, ServiceStatus::ACCEPTED])),
            VoucherExceptions::VoucherIncorrectStatus()
        );

        $dateTimeObject = Carbon::parse($sale->createdAt)->startOfDay();
        throw_if(
            Carbon::now()->startOfDay()->diffInDays($dateTimeObject) !== 0,
            VoucherExceptions::VoucherExpired()
        );

        return true;
    }

    public function burnVoucher(Service $service, string $imei, array $metadata = null): Service
    {
        $burnedStructure = [
            'burned' => [
                'current' => [
                    'imei' => $imei,
                    'metadata' => $metadata,
                    'createdAt' => Carbon::now()
                ],
                'log' => []
            ]
        ];

        $log = data_get($service->burned, 'log');
        if (null !== $log) {
            $burnedStructure['burned']['log'] = $log;
        }

        return $this->saleRepository->updateService($service, $burnedStructure);
    }

    public function checkVoucherIsAvailable(string $serviceTransaction): Collection
    {
        $service = $this->getServiceByTransaction($serviceTransaction);
        $this->validateVoucher($service);

        return $service->sector === Operations::TRADE_IN ?
            $this->validateTradeInItems(collect([$service->sale])) :
            $this->validateOperatorItems(collect([$service->sale]));
    }

    public function availableDiscounts(string $cpf): Collection
    {
        $user = Auth::user();

        $itemsTradeIn = $this->saleRepository->getByFilters([
            'cpfCustomerWithoutLike' => $cpf,
            'status' => ServiceStatus::ACCEPTED,
            'burned' => false,
            'sector' => Operations::TRADE_IN,
            'network' => $user->getNetwork()->id
        ]);

        $itemsOperator = $this->saleRepository->getByFilters([
            'cpfCustomerWithoutLike' => $cpf,
            'sector' => Operations::TELECOMMUNICATION,
            'burned' => false,
            'network' => $user->getNetwork()->id
        ]);

        $collectionTradeIn  = $this->validateTradeInItems($itemsTradeIn);
        $collectionOperator = $this->validateOperatorItems($itemsOperator);
        return $collectionTradeIn->merge($collectionOperator);
    }

    private function validateTradeInItems(Collection $itemsTradeIn): Collection
    {

        $filtered = $itemsTradeIn->reject(static function ($transaction) {
            $dateTimeObject = Carbon::parse($transaction->createdAt)->startOfDay();
            return Carbon::now()->startOfDay()->diffInDays($dateTimeObject) !== 0;
        });

        $collection = collect();

        $filtered->each(static function ($sale) use (&$collection) {
            $sale->services()->each(static function ($service) use (&$collection) {
                $imeiDevice = data_get($service, 'device.imei');

                $collection->push([
                    'operator' => $service->operator,
                    'sector' => $service->sector,
                    'operation' => $service->operation,
                    'transaction_id' => $service->serviceTransaction,
                    'value' => $service->price,
                    'imei' => $imeiDevice,
                    'device' => data_get($service, 'device', null)
                ]);
            });
        });

        return $collection;
    }

    private function validateOperatorItems(Collection $itemsOperators): Collection
    {

        $filtered = $itemsOperators->reject(static function ($transaction) {
            $dateTimeObject = Carbon::parse($transaction->createdAt)->startOfDay();
            return Carbon::now()->startOfDay()->diffInDays($dateTimeObject) !== 0;
        });

        $collection = collect();

        $filtered->each(function ($sale) use (&$collection) {
            $sale->services()->each(function ($service) use (&$collection) {
                $allowedStatus = [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED];
                if (! in_array($service->status, $allowedStatus, true)) {
                    return;
                }

                if (empty($service->device) || $service->isPreSale === true) {
                    return;
                }

                $collection->push([
                    'operator' => $service->operator,
                    'sector' => $service->sector,
                    'transaction_id' => $service->serviceTransaction,
                    'value' => $this->getDiscountValueByService($service),
                    'plan' => $service->label,
                    'planValue' => $service->price,
                    'imei' => $service->imei,
                    'model' => $service->device === null ? $service->product : $service->device['label']
                ]);
            });
        });

        return $collection;
    }

    private function getDiscountValueByService(Service $service): float
    {
        $discount = data_get($service->discount, 'discount', 0);

        // Return discount by triangulation
        if ($discount > 0) {
            return $discount;
        }

        // Mount discount by rebate
        $priceWith = (float) data_get($service->device, 'priceWith', 0);
        $priceWithout = (float) data_get($service->device, 'priceWithout', 0);

        $discount = $priceWithout - $priceWith;

        return $discount > 0 ? $discount : 0;
    }

    public function cancel(string $transactionId, array $requestData, bool $withChargeback = true): ?Service
    {
        return $withChargeback ?
            $this->cancelWithChargeback($transactionId, $requestData) :
            $this->cancelWithoutChargeback($transactionId, $requestData);
    }

    public function cancelWithoutChargeback(string $transactionId, array $requestData): ?Service
    {
        $metadata = data_get($requestData, 'metadata');
        $service  = $this->getServiceByTransaction($transactionId);
        $this->validateCancelVoucher($service, $metadata);

        return $this->cancelVoucher($service);
    }

    public function cancelWithChargeback(string $transactionId, array $requestData): ?Service
    {
        $metadata = data_get($requestData, 'metadata');
        $service  = $this->getServiceByTransaction($transactionId);
        $this->validateCancelVoucher($service, $metadata);
        $updatedService = $this->chargebackVoucher($service);

        return $this->cancelVoucher($updatedService);
    }

    private function validateCancelVoucher(Service $service, array $metadata = null): bool
    {
        throw_if(
            null === $service,
            VoucherExceptions::NotFound()
        );

        $sale           = $service->sale;
        $dateTimeObject = Carbon::parse($sale->createdAt)->startOfDay();
        throw_if(
            Carbon::now()->startOfDay()->diffInDays($dateTimeObject) !== 0,
            VoucherExceptions::VoucherExpired()
        );

        throw_if(
            null === $service->burned || empty($service->burned['current']),
            VoucherExceptions::VoucherNotBurnedWhenTryingCancel()
        );

        throw_if(
            $metadata !== null && $service->burned['current']['metadata'] !== null
            && false === $this->checkValidMetadata($service, $metadata),
            VoucherExceptions::IncorrectValuesFromVoucherMetadata()
        );

        return true;
    }

    public function cancelVoucher(Service $service): Service
    {
        $burnedStructure                    = [
            'burned' => [
                'current' => null,
                'log' => []
            ]
        ];
        $burnedStructure['burned']['log']   = $service->burned['log'];
        $burnedStructure['burned']['log'][] = $service->burned['current'];
        return $this->saleRepository->updateService($service, $burnedStructure);
    }

    private function checkValidMetadata(?Service $service, $metadata): bool
    {
        $currentMetadata = data_get($service->burned, 'current.metadata');

        if (empty(array_diff_assoc($currentMetadata, $metadata))) {
            return true;
        }
        return false;
    }

    private function chargebackVoucher(?Service $service): Service
    {
        return $service->sector === Operations::TRADE_IN ?
            $this->chargebackTradeInVoucher($service) :
            $this->chargebackTriangulationVoucher($service);
    }

    private function chargebackTradeInVoucher(?Service $service): Service
    {
        return $this->saleRepository->updateService($service, ['status' => ServiceStatus::CANCELED]);
    }

    private function chargebackTriangulationVoucher(?Service $service): Service
    {
        $serviceLog   = $service->log;
        $serviceLog[] = [
            'type' => 'success',
            'message' => 'Desconto estornado com sucesso.',
            'data' => $service->discount
        ];
        return $this->saleRepository->updateService($service, [
            'log' => $serviceLog,
            'discount' => null
        ]);
    }

    public function getNewDiscountDevice(string $transactionId, string $newIMEI)
    {

        $service = $this->getServiceByTransaction($transactionId);
        $this->validateChangeDeviceVoucher($service);

        $user          = Auth::user();
        $discountData  = $this->mountDiscountTriangulationData($newIMEI, $service);
        $triangulation = $this->getAvailableTriangulation($user, $discountData, $service);

        $discountResult = [
            'operator' => $service->operator,
            'sector' => $service->sector,
            'plan' => $service->label,
            'transaction_id' => $service->serviceTransaction,
            'current' => [
                'value' => $service->discount['discount'],
                'imei' => $service->imei,
                'model' => $service->device['label']
            ],
            'new' => [
                'value' => $triangulation->devices->first()->discount,
                'imei' => $newIMEI,
                'model' => $triangulation->devices->first()->device->label
            ]
        ];

        return $discountResult;
    }

    private function validateChangeDeviceVoucher(Service $service): bool
    {
        throw_if(
            null === $service,
            VoucherExceptions::NotFound()
        );

        throw_if(
            $service->discount === null && $service->sector !== Operations::TELECOMMUNICATION,
            VoucherExceptions::OnlyOperatorSaleIsAllowed()
        );

        $sale = $service->sale;

        $user = Auth::user();
        throw_if(
            $sale->pointOfSale['network']['id'] !== $user->getNetwork()->id,
            VoucherExceptions::VoucherNotBelongsToNetwork()
        );

        $dateTimeObject = Carbon::parse($sale->createdAt)->startOfDay();
        throw_if(
            Carbon::now()->startOfDay()->diffInDays($dateTimeObject) !== 0,
            VoucherExceptions::VoucherExpired()
        );

        return true;
    }

    private function getAvailableTriangulation(User $user, array $discountData, ?Service $service)
    {
        $discountSale = $this->discountService->triangulationInSale($user, $discountData);

        throw_if(
            $discountSale->triangulations->count() === 0,
            VoucherExceptions::NoTriangulationForImei()
        );

        $triangulation = $discountSale->triangulations->first();

        $filtered = $triangulation->products->filter(static function ($product) use ($service) {

            if ($product->operator === Operations::TIM &&
                $product->operation === Operations::TIM_CONTROLE_FATURA &&
                $product->operator === $service->operator &&
                $product->operation === $service->operation
            ) {
                return $product;
            }

            if ($service->operator === $product->operator &&
                $service->operation === $product->operation &&
                $service->product == $product->product) {
                return $product;
            }
        });

        throw_if(
            $filtered->count() === 0,
            VoucherExceptions::NoOtherTriangulationInOperator()
        );

        return $triangulation;
    }

    private function mountDiscountTriangulationData(string $imei, ?Service $service): array
    {
        $discountData = [
            'deviceIdentifier' => $imei,
            'operator' => $service->operator,
            'operation' => $service->operation
        ];
        return $discountData;
    }

    public function applyDiscountForDevice(string $transactionId, string $imei): array
    {
        $service = $this->getServiceByTransaction($transactionId);
        $this->validateChangeDeviceVoucher($service);

        $user           = $user = Auth::user();
        $discountData   = $this->mountDiscountTriangulationData($imei, $service);
        $discount       = $this->getAvailableTriangulation($user, $discountData, $service);
        $deviceDiscount = $discount->devices->first();

        $serviceArray                         = $service->toArray();
        $serviceArray['imei']                 = $imei;
        $serviceArray['device']               = $deviceDiscount->device;
        $serviceArray['discount']['id']       = $discount->id;
        $serviceArray['discount']['title']    = $discount->title;
        $serviceArray['discount']['discount'] = $deviceDiscount->discount;

        $newService = ServicesFactory::make($serviceArray);

        $updatedService = $this->saleRepository->updateService($service, [
            'device' => $newService->device,
            'discount' => $newService->discount
        ]);

        return [
            'operator' => $updatedService->operator,
            'sector' => $updatedService->sector,
            'plan' => $updatedService->label,
            'transaction_id' => $updatedService->serviceTransaction,
            'value' => $updatedService->discount['discount'],
            'imei' => $updatedService->imei,
            'model' => $updatedService->device['label']
        ];
    }
}

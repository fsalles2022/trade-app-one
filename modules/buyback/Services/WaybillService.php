<?php

namespace Buyback\Services;

use Buyback\Adapters\SalesWaybillAdapter;
use Buyback\Enumerators\WaybillOperations;
use Buyback\Exceptions\WaybillEmptyException;
use Buyback\Repositories\WaybillRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\Permissions\WaybillPermission;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Facades\UserPolicies;
use TradeAppOne\Mail\WayBillShip;

class WaybillService
{
    private $waybillRepository;
    private $waybillJob;
    private $pointOfSaleService;

    public function __construct(
        WaybillRepository $waybillRepository,
        WaybillJob $waybillJob,
        PointOfSaleService $pointOfSaleService
    ) {
        $this->waybillRepository  = $waybillRepository;
        $this->waybillJob         = $waybillJob;
        $this->pointOfSaleService = $pointOfSaleService;
    }

    public function generateWaybill(User $user, string $cnpj, string $operation = null): string
    {
        UserPolicies::setUser($user)->hasAuthorizationUnderPointOfSale($cnpj);
        $pointOfSale = $this->pointOfSaleService->findOneByCnpj($cnpj);

        $operations     = $this->getOperationsTradeInAvailable($user);
        $operations     = $operation !== null && in_array($operation, $operations, true) ? [$operation] : $operations;
        $waybillUpdated = $this->consolidateWaybill($user, $pointOfSale, $operations);

        $sender = new WayBillShip($waybillUpdated, [ $user->email ]);
        Mail::send($sender);
        $sender->clearAttachs();

        return $this->waybillJob->downloadAsPdf($waybillUpdated);
    }

    /**
     * @param User $user
     * @param PointOfSale $pointOfSale
     * @param array $operations
     * @return Waybill
     * @throws WaybillEmptyException
     */
    public function consolidateWaybill(User $user, PointOfSale $pointOfSale, array $operations): Waybill
    {
        $pointOfSale = Collection::wrap($pointOfSale);
        $sales       = $this->waybillRepository->findServicesWithWaybillAvailable($pointOfSale, $operations, true);
        $waybill     = SalesWaybillAdapter::adapter($sales, $pointOfSale, $operations)->first();

        if (empty($waybill)) {
            throw new WaybillEmptyException();
        }

        return $this->waybillRepository->persistWaybill($user, $waybill);
    }

    public function getWaybillsAvailable(User $user, array $filters = []): Collection
    {
        $filterPointsOfSale = data_get($filters, 'pointsOfSale', null);
        $filterOperations   = data_get($filters, 'operations', null);

        $operations = ! empty($filterOperations) ? $filterOperations : $this->getOperationsTradeInAvailable($user);

        $pointsOfSale = $this->getPointOfSaleAvailableService($user, $operations);

        $pointsOfSale = $pointsOfSale->when($filterPointsOfSale, static function ($collection) use ($filterPointsOfSale) {
            return $collection->whereIn('cnpj', $filterPointsOfSale);
        });

        $sales = $this->waybillRepository->findServicesWithWaybillAvailable($pointsOfSale, $operations, false);

        return SalesWaybillAdapter::adapter($sales, $pointsOfSale, $operations);
    }

    public function getPointOfSaleAvailableService(User $user, array $operations): Collection
    {
        [
            $networksAvailable,
            $pointsOfSaleAvailable
        ] = $this->getPointOfSalesAndNetworksByUser($user);

        $networksWithAvailableOperations = NetworkRepository::availableServiceRelation($operations, 'operation')
            ->whereIn('id', $networksAvailable->pluck('id'))->get();

        $pointsOfSale = collect();

        foreach ($networksAvailable as $networkAvailable) {
            $networks = $networksWithAvailableOperations->where('id', '=', $networkAvailable->id);

            if ($networks->isNotEmpty()) {
                $pointsOfSaleSelected = $pointsOfSaleAvailable->whereIn('networkId', $networks->first()->id);
            } else {
                $pointsOfSaleSelected = PointOfSaleRepository::availableServiceRelation($operations, 'operation')
                    ->whereIn('id', $pointsOfSaleAvailable->pluck('id'))
                    ->get();
            }

            $pointsOfSale = $pointsOfSale->merge($pointsOfSaleSelected);
        }

        return $pointsOfSale;
    }

    private function userHasAllPointOfSalePermissions(User $user): bool
    {
        if ($user->hasPermission(WaybillPermission::getFullName(WaybillPermission::ALL))) {
            return true;
        }

        return false;
    }

    /** @return Collection[] */
    private function getPointOfSalesAndNetworksByUser(User $user): array
    {
        if ($this->userHasAllPointOfSalePermissions($user)) {
            $authorizations = UserPolicies::setUser($user);

            return [
                $authorizations->getNetworksAuthorized(),
                $authorizations->getPointsOfSaleAuthorized(),
            ];
        }

        $user->load('pointsOfSale.network');

        return [
            $user->pointsOfSale->pluck('network')->unique('id'),
            $user->pointsOfSale,
        ];
    }

    public function getOperationsTradeInAvailable(User $user): array
    {
        $operations = $user->getNetwork()->getTradeInMobileOperations();
        $intersect  = array_intersect($operations, WaybillOperations::AVAILABLES);
        if (count($intersect) > 0) {
            return $intersect;
        }

        $operations = collect([]);
        $user->pointsOfSale()->get()->each(function ($pointOfSale) use ($operations) {
            $operationList = $pointOfSale
                ->services()
                ->where(['sector'=> Operations::TRADE_IN, 'operator' => Operations::TRADE_IN_MOBILE])
                ->get()
                ->pluck('operation');

            $operationList->each(function ($operationItem) use ($operations) {
                $operations->push($operationItem);
            });
        });

        if ($operations->count() > 0) {
            return array_intersect($operations->toArray(), WaybillOperations::AVAILABLES);
        }

        return [];
    }

    /**
     * @param User $user
     * @param array $attributes
     * @return Service|null
     * @throws SaleNotFoundException
     */
    public function checkWithdrawnDevice(User $user, array $attributes): ?Service
    {
        return $this->waybillRepository->checkWaybillDevice(
            $user,
            data_get($attributes, 'serviceTransaction', '')
        );
    }
}

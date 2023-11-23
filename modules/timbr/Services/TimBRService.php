<?php

namespace TimBR\Services;

use Discount\Services\DeviceTimService;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use TimBR\Adapters\ElDorado\CreditCardResponseAdapter;
use TimBR\Adapters\TimBRCepResponseAdapter;
use TimBR\Adapters\TimCheckMasterMsisdnResponseAdapter;
use TimBR\Adapters\TimEligibilityResponseAdapter;
use TimBR\Adapters\TimOrderApprovalResponseAdapter;
use TimBR\Adapters\TimSimCardActivationResponseAdapter;
use TimBR\Connection\TimBRConnection;
use TimBR\Connection\TimBRElDorado\TimBRElDoradoConnection;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Enumerators\TimBRStatus;
use TimBR\Exceptions\EligibilityNotFound;
use TimBR\Exceptions\PointOfSaleIdentifierNotFound;
use TimBR\Exceptions\TimBRAreaCodeZipCode;
use TimBR\Exceptions\TimBROrder;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\BrasilAreaCodes;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleStateNotFound;
use TradeAppOne\Features\Customer\Adapter\CustomerNested;

class TimBRService extends BaseService
{
    public const MAX_TRIES_CHECK_ORDER_STATUS = 12;

    /** @var TimBRConnection  */
    protected $connection;

    /** @var TimBRElDoradoConnection  */
    protected $eldorado;

    /** @var DeviceTimService */
    protected $deviceTimService;

    public function __construct(TimBRConnection $connection, TimBRElDoradoConnection $eldorado, DeviceTimService $deviceTimService)
    {
        $this->connection       = $connection;
        $this->eldorado         = $eldorado;
        $this->deviceTimService = $deviceTimService;
    }

    public function getDomains(User $user, Request $request): Collection
    {
        $network = $user->getNetwork()->slug;
        $cpf     = $user->cpf;

        $utils = $this->connection->getDomains($network, $cpf);
        $utils = self::removingTimStuffThatTheyCantDo($utils);

        if ($request->get('type') === 'dueDate') {
            $utils = $utils->where('type', 'TCOM_LOV_DATAVENCIMENTO');
        }
        if ($request->get('type') === 'banks') {
            $utils = $utils->where('type', 'TCOM_LOV_BANCOS');
        }
        if ($request->get('type') === 'invoiceType') {
            $utils = $utils->where('type', 'TCOM_LOV_MEIO_PAGAMENTO');
        }
        if ($request->get('type') === 'billingType') {
            $utils = $utils->where('type', 'TCOM_LOV_TIPOFATURA');
        }
        if ($request->get('type') === 'acceptanceType') {
            $utils = $utils->where('type', 'TIPO_ACEITE');
        }
        if ($request->get('type') === 'documents') {
            $utils = $utils->where('type', 'TOM_TIPO_DOC');
        }
        if ($request->get('type') === 'local') {
            $utils = $utils->where('type', 'AMS_LOV_ADDR_IND');
        }
        return $utils;
    }

    public static function removingTimStuffThatTheyCantDo(Collection $domains)
    {
        return $domains->whereNotIn('label', ['Cartao de Credito', 'Boleto'])->values();
    }

    public function eligibility(User $user, string $pointOfSaleId, array $payload): ?TimEligibilityResponseAdapter
    {
        event(new PreAnalysisEvent(new CustomerNested($payload)));

        try {
            $pointOfSale = $this->pointOfSaleService
                ->checkPermissionAndReturnPointOfSale($user, $pointOfSaleId);

            $network                = $pointOfSale->network->slug;
            $cpf                    = $user->cpf;
            $payload['pointOfSale'] = $pointOfSale->providerIdentifiers[Operations::TIM];
            $payload['state']       = $pointOfSale->state;

            $requireDeviceLoyalty = data_get($payload, 'requireDeviceLoyalty', false);
            $device = null;

            if ($requireDeviceLoyalty) {
                $device = $this->deviceTimService->findById((int) data_get($payload, 'deviceId', 0));
            }

            if ($this->validateAreaCodeStateCode($user, $payload, $pointOfSale->state)) {
                $response = $this->connection->eligibility($network, $cpf, $payload);

                return new TimEligibilityResponseAdapter($response, $payload, $device);
            }

            throw new TimBRAreaCodeZipCode();
        } catch (ErrorException $exception) {
            throw new PointOfSaleIdentifierNotFound($exception->getMessage());
        }
    }

    /** @throws EligibilityNotFound */
    private function getCachedEligibility($customerCpf): Eligibility
    {
        $eligibility = Cache::get(TimBRCacheables::ELIGIBILITY . $customerCpf);

        if ($eligibility === null) {
            throw new EligibilityNotFound();
        }

        return $eligibility;
    }

    /**
     * @throws PointOfSaleIdentifierNotFound
     * @param mixed[] $payload
     */
    public function checkMasterMsisdn(User $user, string $pointOfSaleId, string $masterMsisdn, array $payload): TimCheckMasterMsisdnResponseAdapter
    {
        try {
            $pointOfSale = $this->pointOfSaleService->checkPermissionAndReturnPointOfSale($user, $pointOfSaleId);
            $network     = $pointOfSale->network->slug;
            $cpf         = $user->cpf;

            $payloadToOrder = TimBRMapCheckMasterMsisdnService::map($payload);

            $response = $this->connection->checkMasterMsisdn($network, $cpf, $masterMsisdn, $payloadToOrder);

            return new TimCheckMasterMsisdnResponseAdapter($response);
        } catch (ErrorException $exception) {
            throw new PointOfSaleIdentifierNotFound($exception->getMessage());
        }
    }

    /**
     * @throws PointOfSaleIdentifierNotFound
     * @param mixed[] $payload
     */
    public function orderApproval(User $user, string $pointOfSaleId, array $payload):  TimOrderApprovalResponseAdapter
    {
        try {
            $pointOfSale = $this->pointOfSaleService->checkPermissionAndReturnPointOfSale($user, $pointOfSaleId);
            $address     = $this->cep($user, $payload['customer']['zipCode'] ?? '');
            $network     = $pointOfSale->network->slug;
            $cpf         = $user->cpf;

            $payloadToOrder = TimBRMapOrderApprovalService::map(
                $this->getCachedEligibility((string) $payload['customer']['cpf'] ?? ''),
                $pointOfSale,
                $address->getAdapted(),
                $payload
            );

            $response = $this->connection->orderApproval($network, $cpf, $payloadToOrder);

            return new TimOrderApprovalResponseAdapter($response);
        } catch (ErrorException $exception) {
            throw new PointOfSaleIdentifierNotFound($exception->getMessage());
        }
    }

    /**
     * @throws PointOfSaleIdentifierNotFound
     * @param mixed[] $payload
     */
    public function simCardActivation(User $user, array $payload):  TimSimCardActivationResponseAdapter
    {
        try {
            $service     = $this->saleService->findService($payload['serviceTransaction'] ?? '');
            $pointOfSale = $service->sale->pointOfSale;
            $network     = $pointOfSale['network']['slug'];
            $cpf         = $user->cpf;

            $response = $this->connection->simCardActivation(
                $network,
                $cpf,
                TimBRMapSimCardActivationService::map($service->toArray())
            );

            $this->saleService->updateService($service, [
                'msisdn' => MsisdnHelper::addCountryCode(CountryAbbreviation::BR, $response->get('device.msisdn'))
            ]);

            $this->saleService->pushLogService($service, [
                'message' => trans('timBR::messages.sim_card_activation.success'),
                'data' => $response->toArray()
            ]);

            return new TimSimCardActivationResponseAdapter($response);
        } catch (ErrorException $exception) {
            throw new PointOfSaleIdentifierNotFound($exception->getMessage());
        }
    }

    /** @throws TimBROrder */
    public function generateOrder(string $protocol, string $network, string $cpf, array $payload): Responseable
    {
        try {
            return $this->connection->order($network, $cpf, $payload);
        } catch (TimBROrder $exception) {
            if ($exception->getHttpStatus() !== Response::HTTP_INTERNAL_SERVER_ERROR) {
                throw $exception;
            }
            
            $received = $this->checkReceivedOrderByProtocol($protocol, $network);

            if ($received === false) {
                throw $exception;
            }

            return $exception->getResponse();
        }
    }

    /** Check if TIM received order to go to next step in the sale flow */
    private function checkReceivedOrderByProtocol(string $protocol, string $network): bool
    {
        $tries    = 0;
        $finished = false;
        $received = false;

        while (! $finished) {
            // try 12 times every 5 seconds (1 min)
            if ($tries >= self::MAX_TRIES_CHECK_ORDER_STATUS) {
                $finished = true;
                continue;
            }

            try {
                sleep(5);// Necessary await to check, this time is necessary for TIM to create the order.

                $response = $this->connection
                    ->selectCustomConnection($network)
                    ->getOrderStatusByProtocol($protocol);

                $statusFromTim = data_get($response->toArray(), 'status', '');

                if ($response->isSuccess() === false || in_array($statusFromTim, TimBRStatus::NOT_FOUND, true)) {
                    $tries++;
                    continue;
                }

                $finished = true;
                $received = true;
            } catch (\Throwable $exception) {
                $tries++;
            }
        }

        return $received;
    }

    private function validateAreaCodeStateCode(User $user, array $payload, ?string $pointOfSaleStateCode): bool
    {
        if ($pointOfSaleStateCode) {
            $eligibilityAreaCode  = $this->getAreaCode($payload);
            $zipCode              = data_get($payload, 'customer.zipCode');
            $address              = $this->cep($user, $zipCode)->getAdapted();
            $cepStateCode         = data_get($address, 'stateOrProvince');
            $cepAreaCodes         = data_get(BrasilAreaCodes::STATES_AREA_CODES, $cepStateCode);
            $pointOfSaleAreaCodes = data_get(BrasilAreaCodes::STATES_AREA_CODES, $pointOfSaleStateCode);

            return $pointOfSaleAreaCodes === $cepAreaCodes && in_array($eligibilityAreaCode, $pointOfSaleAreaCodes, true);
        }
        throw new PointOfSaleStateNotFound();
    }

    private function getAreaCode(array $payload): ?string
    {
        $areaCode = data_get($payload, 'areaCode', false);
        if ($areaCode === false) {
            $completeNumber = data_get($payload, 'msisdn') ?? data_get($payload, 'portedNumber', '');
            $code           =  MsisdnHelper::getAreaCode($completeNumber);
            return $code;
        }
        return $areaCode;
    }

    public function cep(User $user, string $cep): TimBRCepResponseAdapter
    {
        $network = $user->getNetwork()->slug;
        $cpf     = $user->cpf;
        return new TimBRCepResponseAdapter($this->connection->getCep($network, $cpf, $cep));
    }

    public function registerCreditCard(array $creditCard): CreditCardResponseAdapter
    {
        $pan   = data_get($creditCard, 'pan');
        $month = data_get($creditCard, 'month');
        $year  = data_get($creditCard, 'year');

        return new CreditCardResponseAdapter($this->eldorado->registerCreditCard($pan, $month, $year));
    }
}

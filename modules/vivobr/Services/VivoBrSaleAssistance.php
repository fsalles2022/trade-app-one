<?php


namespace VivoBR\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use TradeAppOne\Exceptions\ThirdPartyExceptions;
use TradeAppOne\Facades\UserPolicies;
use VivoBR\Adapters\SunConfirmControleCartaoRequestAdapter;
use VivoBR\Assistances\VivoBRAssistanceFactory;
use VivoBR\Connection\SunConnection;
use VivoBR\Exceptions\VivoBRAPIPersistenceException;
use VivoBR\Helpers\VivoBrPlansFilter;
use VivoTradeUp\Assistances\VivoTradeUpAssistanceFactory;

class VivoBrSaleAssistance implements AssistanceBehavior
{
    protected $sunConnection;
    protected $saleRepository;

    public function __construct(
        SunConnection $sunConnection,
        SaleRepository $saleRepository
    ) {
        $this->sunConnection  = $sunConnection;
        $this->saleRepository = $saleRepository;
    }

    public function getProductsByFilters(string $networkSlug, ?array $filters): Collection
    {
        $areaCode = data_get($filters, 'areaCode', null);
        $query    = $areaCode ? ['ddd' => $areaCode] : [];

        $sunResponse = $this->sunConnection
            ->selectCustomConnection($networkSlug)
            ->listPlans($query);

        $plans = VivoBrMapPlansService::map($sunResponse->toArray());
        return VivoBrPlansFilter::filter($plans, $filters, Auth::user());
    }

    public function createUser(array $attributes): Responseable
    {
        return $this->sunConnection->createUser($attributes);
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $serviceOptions = ServiceOptionsFilter::make(Auth::user(), [
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::VIVO,
            'operation' => Operations::VIVO_CONTROLE_CARTAO
        ])->verifyM4uTradeUp()->filter();

        if (in_array(ServiceOptionsFilter::VIVO_CONTROLE_CARTAO_M4U, $serviceOptions, true)) {
            $assistance = VivoTradeUpAssistanceFactory::make($service->operation);
        } else {
            $assistance = VivoBRAssistanceFactory::make($service->operation);
        }
        return $assistance->integrateService($service);
    }

    /** @throws */
    public function confirmControleCartao($payload)
    {
        $service = $this->saleRepository->findInSale($payload['serviceTransaction']);
        $adapted = SunConfirmControleCartaoRequestAdapter::adapt($service, $payload);
        $network = $service->sale->pointOfSale['network']['slug'];
        $this->saleRepository->pushLogService($service, $payload);
        try {
            $resultOfConfirmation = $this->sunConnection
                ->selectCustomConnection($network)
                ->confirmControleCartao($adapted)
                ->toArray();
            $this->saleRepository->pushLogService($service, $resultOfConfirmation);
            if ($payload['status'] == 'SUCCESS' && $resultOfConfirmation['codigo'] == 0) {
                $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
                return true;
            }
            return false;
        } catch (ThirdPartyExceptions $exception) {
            return false;
        }
    }

    public function getCustomerTotalization(string $cpf): Responseable
    {
        $network  = UserPolicies::getNetworksAuthorized()->first();
        $response = $this->sunConnection->selectCustomConnection($network->slug)->customerTotalization($cpf);

        if ($response->isSuccess()) {
            return $response;
        }

        throw new VivoBRAPIPersistenceException($response->get('mensagem'));
    }
}

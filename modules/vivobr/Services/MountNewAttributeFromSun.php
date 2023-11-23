<?php

namespace VivoBR\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use VivoBR\Connection\SunConnection;
use VivoBR\Enumerators\VivoOperations;
use VivoTradeUp\Repositories\VivoM4uControleCartao;
use VivoTradeUp\Repositories\VivoPre;

class MountNewAttributeFromSun implements MountNewAttributesService
{
    protected $sunConnection;
    protected $adapter = [];

    public function __construct(SunConnection $sunConnection)
    {
        $this->sunConnection = $sunConnection;
    }

    public function getAttributes(array $service): array
    {
        $requestUser = Auth::user();
        $pointOfSale = $requestUser->pointsOfSale()->first();
        $networkSlug = $pointOfSale->network->slug;

        if (isset($service['areaCode'])) {
            $areaCode = $service['areaCode'];
        }
        if (isset($service['msisdn'])) {
            $areaCode = substr($service['msisdn'], 0, 2);
        }
        if (isset($service['portedNumber'])) {
            $areaCode = substr($service['portedNumber'], 0, 2);
        }

        if (Operations::VIVO_PRE === data_get($service, 'operation')) {
            $plan = VivoPre::getVivoPre();
            return $this->adapterPlan($plan, $service, $areaCode);
        }

        $serviceOptions = ServiceOptionsFilter::make($requestUser, [
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::VIVO,
            'operation' => Operations::VIVO_CONTROLE_CARTAO
        ])->verifyM4uTradeUp()->filter();

        if (in_array(ServiceOptionsFilter::VIVO_CONTROLE_CARTAO_M4U, $serviceOptions, true)) {
            $plan = collect(
                VivoM4uControleCartao::getControleCartaoM4u($networkSlug, $areaCode)
            )->where('id', $service['product'])->first();
            return $this->adapterPlan($plan, $service, $areaCode);
        }

        $response = $this->sunConnection->selectCustomConnection($networkSlug)->listPlans(['ddd' => $areaCode])->toArray();
        return $this->adapterPlan($response['planos'], $service, $areaCode);
    }

    public function adapterPlan(array $plans, array $service, $areaCode): array
    {
        $plan = collect([])->push($plans)->where('id', $service['product'])->first();

        $vivoOperation = ConstantHelper::getValue(
            VivoOperations::class,
            data_get($plan, 'tipo')
        );

        $this->adapter = array_filter([
            'price' => $plan['valor'],
            'label' => mb_strtoupper($plan['nome']),
            'areaCode' => $areaCode,
            'planSlug' => data_get($plan, 'slug', '')
        ]);
        
        if (Operations::VIVO_PRE === $service['operation']) {
            return $this->adapterPrePago($service);
        }

        if ($vivoOperation === Arr::get($service, 'operation')) {
            return $this->adapter;
        }

        throw new ProductNotFoundException();
    }

    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    private function adapterPrePago(array $service): array
    {
        $this->adapter['status'] = ServiceStatus::APPROVED;

        if ($rechargeValue = Arr::get($service, 'rechargeValue')) {
            $this->adapter['rechargeValue'] = (float) $rechargeValue;
        }

        return $this->adapter;
    }
}

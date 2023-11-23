<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Service;

class NetworkBuilder
{
    private $withoutServices;
    private $slug;
    private $service;
    private $channel;

    public static function make(): NetworkBuilder
    {
        return new self();
    }

    public function withoutAvailableServices() : NetworkBuilder
    {
        $this->withoutServices = true;
        return $this;
    }

    public function withSlug(string $slug) : NetworkBuilder
    {
        $this->slug = $slug;
        return $this;
    }

    public function withRandomServices(int $amount = 5) : NetworkBuilder
    {
        $this->withServices(factory(Service::class)->times($amount)->create()->toArray());

        return $this;
    }

    public function withAllServices(int $amount = 5) : NetworkBuilder
    {
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_PLUS']);
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_PROFESSIONAL']);
        $services[] = factory(Service::class)->create(['sector' => 'COURSES', 'operator' => 'UOL', 'operation' => 'UOL_STANDARD']);
        $services[] = factory(Service::class)->create(['sector' => 'INSURANCE', 'operator' => 'GENERALI', 'operation' => 'GENERALI_ELECTRONICS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_BANDA_LARGA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_POS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE_CHIP_COMBO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CONTROLE_FACIL']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'NEXTEL', 'operation' => 'NEXTEL_CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'NEXTEL', 'operation' => 'NEXTEL_CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'OI', 'operation' => 'OI_CONTROLE_BOLETO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'OI', 'operation' => 'OI_CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'CONTROLE_FATURA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'EXPRESS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_CONTROLE_FATURA']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_EXPRESS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'TIM', 'operation' => 'TIM_PRE_PAGO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'CONTROLE']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'CONTROLE_CARTAO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_INTERNET_MOVEL_POS']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_POS_PAGO']);
        $services[] = factory(Service::class)->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'VIVO', 'operation' => 'VIVO_PRE']);
        $services[] = factory(Service::class)->create(['sector' => 'MOBILE_APPS', 'operator' => 'MOVILE', 'operation' => 'MOVILE_CUBES']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MCAFEE_MULTI_ACCESS']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MCAFEE_MULTI_ACCESS_TRIAL']);
        $services[] = factory(Service::class)->create(['sector' => 'SECURITY_SYSTEM', 'operator' => 'MCAFEE', 'operation' => 'MOBILE_SECURITY']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'BRUSED']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'IPLACE']);
        $services[] = factory(Service::class)->create(['sector' => 'TRADE_IN', 'operator' => 'TRADE_IN_MOBILE', 'operation' => 'SALDAO_INFORMATICA']);

        $this->withServices($services);

        return $this;
    }

    /**
     * @param Service|array $service
     * @return $this
     */
    public function withServices($service = []) : NetworkBuilder
    {
        $this->service = array_pluck(array_wrap($service), 'id');

        return $this;
    }

    public function withChannel($channel) : NetworkBuilder
    {
        $this->channel = $channel;
        return $this;
    }

    public function build() :Network
    {
        $parameter = [];

        if ($this->slug) {
            data_set($parameter, 'slug', $this->slug);
        }
        if ($this->channel) {
            data_set($parameter, 'channel', $this->channel);
        }

        if ($this->withoutServices) {
            return factory(Network::class)->states('without_available_services')->make($parameter);
        }

        $network = factory(Network::class)->create($parameter);

        $this->service && $network->services()->sync($this->service);

        return $network;
    }
}

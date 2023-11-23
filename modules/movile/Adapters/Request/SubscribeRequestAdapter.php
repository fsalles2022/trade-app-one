<?php

namespace Movile\Adapters\Request;

use Movile\Movile;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

class SubscribeRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        return [
            'id'             => $service->serviceTransaction,
            'phone_number'   => $service->msisdn,
            'sku'            => $service->product,
            'origin'         => Movile::origin(),
            'application_id' => Movile::applicationId()
        ];
    }
}

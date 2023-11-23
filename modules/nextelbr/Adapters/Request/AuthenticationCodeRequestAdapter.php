<?php

namespace NextelBR\Adapters\Request;

use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

class AuthenticationCodeRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $cpf                  = data_get($service, 'customer.cpf');
        $operationIdentifiers = data_get($service, 'operatorIdentifiers');
        return [
            "cpf"      => $cpf,
            "protocol" => data_get($operationIdentifiers, 'protocolo'),
            "channel"  => "VENDAS"
        ];
    }
}

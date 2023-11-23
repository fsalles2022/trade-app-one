<?php

namespace OiBR\Adapters;

use OiBR\OiBRIdentifierNotFound;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class OiBRControleCartaoRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $sale         = $service->sale;
        $customer     = data_get($service, 'customer');
        $oiIdentifier = data_get($sale->pointOfSale, 'providerIdentifiers.' . Operations::OI);
        throw_if(is_null($oiIdentifier), new OiBRIdentifierNotFound());
        return [
            "adesao"            => [
                "iccid"         => data_get($service, 'iccid'),
                "ddd"           => data_get($service, 'areaCode'),
                "codigo_oferta" => data_get($service, 'product')
            ],
            "meio_de_pagamento" => [
                "eldorado_token" => $service->token,
                "cvv"            => $service->cvv
            ],
            "comissionamento"   => [
                "estabelecimento" => $oiIdentifier,
                "vendedor"        => data_get($sale->user, 'cpf'),
                "tipo_vendedor"   => "PROMOTOR"
            ],
            "cliente"           => [
                "nome"            => data_get($customer, 'firstName') . ' ' . data_get($customer, 'lastName'),
                "cpf"             => data_get($customer, 'cpf'),
                "data_nascimento" => "1985-07-16",
                "numero_contato"  => MsisdnHelper::removeCountryCode(
                    MsisdnHelper::BR,
                    data_get($customer, 'mainPhone', '')
                ),
                "endereco"        => [
                    "cep"    => data_get($customer, 'zipCode'),
                    "numero" => data_get($customer, 'number')
                ]
            ]
        ];
    }
}

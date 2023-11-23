<?php

namespace TimBR\Adapters\M4u;

use TimBR\Enumerators\TimBRFormats;
use TimBR\Exceptions\M4uAdapterRequestException;
use TimBR\Exceptions\PointOfSaleIdentifierNotFound;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBRM4uRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        try {
            $customer = $service->customer;
            $sale     = $service->sale;
            $birthday = date(TimBRFormats::DATES, strtotime($customer['birthday']));
            $custCode = $sale->pointOfSale['providerIdentifiers'][Operations::TIM];
            throw_if(is_null($custCode), new PointOfSaleIdentifierNotFound());
            if (filled($service->msisdn)) {
                $value = $service->msisdn;
                $type  = 'MSISDN';
            } else {
                $value = $service->iccid;
                $type  = 'ICCID';
            }

            return [
                "id"       => [
                    "value" => $value,
                    "type"  => $type
                ],
                "client"   => [
                    "cpf"        => $customer['cpf'],
                    "cep"        => $customer['zipCode'],
                    "name"       => "{$customer['firstName']} {$customer['lastName']}",
                    "motherName" => $customer['filiation'],
                    "birthDate"  => $birthday,
                    "creditCard" => [
                        'token' => $service->creditCard['token'],
                        'cvv'   => $service->creditCard['cvv'],
                    ]
                ],
                "products" => [
                    "productId"    => $service->product,
                    "areaCode"     => $service->areaCode,
                    'serviceCodes' => []
                ],
                "pdv"      => [
                    "custcode" => $custCode,
                    "uf"       => $sale->pointOfSale['state']
                ],
                "salesMan" => [
                    "id"  => $extra,
                    "cpf" => $sale->user['cpf']
                ]
            ];
        } catch (\ErrorException $exception) {
            throw new M4uAdapterRequestException();
        }
    }
}

<?php

namespace ClaroBR\Adapters;

use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Enumerators\ConfirmOperationStatus;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;

class LogRequestAdapter implements RequestAdapterBehavior
{
    private const APROVADO = 'APROVADO';

    public static function adapt(Service $service, $extra = null):array
    {
        $status    = data_get($extra, 'status');
        $serviceId = data_get($service->operatorIdentifiers, 'servico_id');
        $payment   = data_get($extra, 'payment');

        if ($status === ConfirmOperationStatus::SUCCESS) {
            return array_filter([
                'service_form' => [
                    [
                        'id'     => $serviceId,
                        'log'    => $payment,
                        'status' => self::APROVADO
                    ]
                ]
            ]);
        }

        if ($service->status === ServiceStatus::SUBMITTED) {
            return array_filter([
                'service_form' => [
                    [
                        'id'     => $serviceId,
                        'iccid'  => $service->iccid
                    ]
                ]
            ]);
        }

        return array_filter([
            'service_form' => [
                [
                    'id'     => $serviceId,
                    'log'    => $payment
                ]
            ]
        ]);
    }
}

<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Adapters;

use TradeAppOne\Domain\Models\Collections\Service;

final class RemotePaymentCreditCardResponseAdapter
{
    /** @return mixed[] */
    public static function adaptToCreditCard(Service $service): array
    {
        return [
            'status' => data_get($service, 'status', ''),
            'serviceTransaction' => data_get($service, 'serviceTransaction', ''),
            'price' => data_get($service, 'price', ''),
            'label' => data_get($service, 'label', ''),
            'customer' => data_get($service, 'customer.firstName', '') . ' ' . data_get($service, 'customer.lastName', ''),
            'customerDocument' => data_get($service, 'customer.cpf', ''),
        ];
    }
}

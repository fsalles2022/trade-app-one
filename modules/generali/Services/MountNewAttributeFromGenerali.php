<?php

namespace Generali\Services;

use Generali\Assistance\Connection\GeneraliConnection;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class MountNewAttributeFromGenerali implements MountNewAttributesService
{
    protected $generaliConnection;

    public function __construct(GeneraliConnection $generaliConnection)
    {
        $this->generaliConnection = $generaliConnection;
    }

    public function getAttributes(array $service): array
    {
        $premium = $this->generaliConnection->calcPremium([
            'product' => data_get($service, 'product', []),
            'device'  => data_get($service, 'device', [])
        ])->toArray();

        $price = data_get($premium, 'total');

        return compact('premium', 'price');
    }
}

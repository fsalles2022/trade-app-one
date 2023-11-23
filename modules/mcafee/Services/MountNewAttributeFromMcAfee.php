<?php

namespace McAfee\Services;

use McAfee\Connection\McAfeeConnectionInterface;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class MountNewAttributeFromMcAfee implements MountNewAttributesService
{
    protected $mcAfeeConnection;

    public function __construct(McAfeeConnectionInterface $connection)
    {
        $this->mcAfeeConnection = $connection;
    }

    public function getAttributes(array $service): array
    {
        $user  = auth()->user();
        $plans = $this->mcAfeeConnection->plans($user);
        $plan  = $plans->where('id', $service['product'])->first();

        throw_unless($plan, new ProductNotFoundException());

        $product  = $plan['product'];
        $quantity = $plan['quantity'];
        $price    = $plan['price'];
        $label    = $plan['label'];
        return compact('product', 'quantity', 'label', 'price');
    }
}

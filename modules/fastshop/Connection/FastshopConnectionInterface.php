<?php

namespace FastShop\Connection;

use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Tables\User;

interface FastshopConnectionInterface
{
    public function products(array $filters = []): Responseable;

    public function productPrice(array $params = []): Responseable;
}

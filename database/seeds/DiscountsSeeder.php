<?php

use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class DiscountsSeeder extends Seeder
{
    public function run()
    {
        $pointOfSale = PointOfSale::find(1);
        $user = \TradeAppOne\Domain\Models\Tables\User::first();
        if ($pointOfSale instanceof PointOfSale) {
            (new DiscountBuilder())->withPointOfSale($pointOfSale)->withUser($user)->generateDiscountTimes(50);
        }
    }
}

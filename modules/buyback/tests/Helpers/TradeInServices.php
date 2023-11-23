<?php

namespace Buyback\Tests\Helpers;

use Buyback\Models\Operations\Iplace;
use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class TradeInServices
{
    public static function SaldaoInformaticaMobile(array $params = []) :Service
    {
        return self::loadFactory()
            ->of(Service::class)
            ->make(array_merge($params, ['imei' => '123456789012345']));
    }

    public static function IplaceMobile() :Service
    {
        return self::loadIplaceFactory()
            ->of(Iplace::class)
            ->make();
    }

    public static function TradeNetMobile(array $params = []) :Service
    {
        return self::loadFactory()
            ->of(Service::class)
            ->states(Operations::TRADE_NET)
            ->make(array_merge($params, ['imei' => '123456789012345']));
    }

    private static function loadFactory(): Factory
    {
        $factory = Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/buyback/tests/Helpers/Factories')
        );
        return $factory;
    }

    private static function loadIplaceFactory(): Factory
    {
        $factory = Factory::construct(
            \Faker\Factory::create(),
            base_path('modules/buyback/tests/Helpers/Factories/IplaceFactory')
        );
        return $factory;
    }
}

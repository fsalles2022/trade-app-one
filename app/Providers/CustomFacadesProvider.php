<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Facades;
use TradeAppOne\Domain\Logging\HeimdallInbound;
use TradeAppOne\Tools\Uniqid;

class CustomFacadesProvider extends ServiceProvider
{
    public function register()
    {
        app()->bind(Facades::UNIQID, function () {
            return new Uniqid();
        });
        app()->bind(HeimdallInbound::class, function () {
            return new HeimdallInbound();
        });
    }
}

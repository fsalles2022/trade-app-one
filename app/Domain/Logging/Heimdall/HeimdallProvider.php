<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

use Illuminate\Support\ServiceProvider;

class HeimdallProvider extends ServiceProvider
{
    public function register()
    {
        require_once app_path('Domain/Logging/Heimdall/helper.php');
    }
}

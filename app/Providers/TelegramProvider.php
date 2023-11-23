<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Components\Telegram\Telegram;

class TelegramProvider extends ServiceProvider
{
    public function register()
    {
        app()->bind(Telegram::class, function () {
            $token = config('telegram.uri');
            return new Telegram($token);
        });
    }
}

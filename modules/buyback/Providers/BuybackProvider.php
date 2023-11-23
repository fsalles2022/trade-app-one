<?php

namespace Buyback\Providers;

use Buyback\Console\TradeInCancelServiceCommand;
use Buyback\Console\WaybillGenerateCommand;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Buyback\Assistance\TradeInSaleAssistance;
use Buyback\Models\Quiz;
use Buyback\Policies\QuizPolicy;
use TradeAppOne\Domain\Enumerators\Operations;

class BuybackProvider extends AuthServiceProvider
{
    protected $policies = [
        Quiz::class => QuizPolicy::class
    ];

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'buyback');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/buybackApi.php');
        $this->commands([TradeInCancelServiceCommand::class]);
        $this->registerPolicies();

        $this->commands(WaybillGenerateCommand::class);
    }

    public function register()
    {
        $this->app->singleton(Operations::TRADE_IN_MOBILE, TradeInSaleAssistance::class);
    }
}

<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;
use Jenssegers\Date\Date;
use Jenssegers\Mongodb\Eloquent\Builder;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Observers\ServiceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Date::setLocale($this->app->getLocale());
        Service::observe(ServiceObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Builder::macro('getUserId', static function (): string {
            return 'mongodb';
        });
    }
}

<?php

namespace TradeAppOne\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace       = 'TradeAppOne\Http\Controllers';
    protected $mapfreNamespace = 'Mapfre\Http\Controllers';
    protected $mcafeeNamespace = 'McAfee\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapAuthRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware(['api'])
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));

        Route::middleware(['guest'])
            ->namespace($this->namespace)
            ->group(base_path('routes/free.php'));

        Route::middleware(['api'])
            ->namespace($this->mapfreNamespace)
            ->group(base_path('modules/mapfre/routes/mapfreApi.php'));
    }

    /**
     * Define the "auth" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAuthRoutes()
    {
        Route::middleware(['signin'])
            ->namespace($this->namespace)
            ->group(base_path('routes/auth.php'));
    }
}

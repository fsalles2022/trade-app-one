<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    public function register()
    {
        require_once app_path('Domain/Repositories/BuilderCustomQuery.php');
    }
}

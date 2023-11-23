<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\ServiceProvider;

class HelpersProvider extends ServiceProvider
{
    public function boot()
    {
        require_once app_path('Domain/Logging/integrationLog.php');
        require_once app_path('Policies/Permissions/PermissionsPoliciesInject.php');
    }
}

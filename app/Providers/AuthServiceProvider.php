<?php

namespace TradeAppOne\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Policies\NetworkPolicy;
use TradeAppOne\Policies\PointOfSalePolicy;
use TradeAppOne\Policies\RolePolicy;
use TradeAppOne\Policies\SalePolicy;
use TradeAppOne\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Sale::class        => SalePolicy::class,
        PointOfSale::class => PointOfSalePolicy::class,
        User::class        => UserPolicy::class,
        Role::class        => RolePolicy::class,
        Network::class     => NetworkPolicy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

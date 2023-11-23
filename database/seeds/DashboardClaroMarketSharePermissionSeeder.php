<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class DashboardClaroMarketSharePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_CLARO_MARKET_SHARE,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard Claro Market Share do powerBi',
                'slug'   => Permissions::DASHBOARD_CLARO_MARKET_SHARE,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

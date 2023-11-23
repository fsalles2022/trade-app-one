<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardGoalsRiachueloPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_RIACHUELO_GOALS,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard de Metas da Riachuelo do powerBi',
                'slug'   => Permissions::DASHBOARD_RIACHUELO_GOALS,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

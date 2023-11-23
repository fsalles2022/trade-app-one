<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardGoalsCeAPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_CEA_GOALS,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard Metas C&A do powerBi',
                'slug'   => Permissions::DASHBOARD_CEA_GOALS,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

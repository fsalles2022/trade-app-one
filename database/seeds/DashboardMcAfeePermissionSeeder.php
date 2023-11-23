<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardMcAfeePermissionSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_MCAFEE,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard MCAFEE do powerBi',
                'slug'   => Permissions::DASHBOARD_MCAFEE,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardCommissionTimPermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_COMMISSION_TIM,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard Comissionamento TIM (Especialistas TIM) do powerBi',
                'slug'   => Permissions::DASHBOARD_COMMISSION_TIM,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

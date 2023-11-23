<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardLadsPermission;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardLadsPermissionAllSeeder extends Seeder
{
    public function run():void
    {
        Permission::updateOrCreate(
            [
                'slug' => DashboardLadsPermission::getFullName(DashboardLadsPermission::VIEW_ALL),
            ],
            [
                'label'  => 'Adicionar permissão ao dashboardLads total do powerBi',
                'slug'   =>  DashboardLadsPermission::getFullName(DashboardLadsPermission::VIEW_ALL),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }


}

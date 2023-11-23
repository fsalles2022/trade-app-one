<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\WaybillPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class WaybillPermissionViewAllSeeder extends Seeder
{
    public function run():void
    {
        Permission::updateOrCreate(
            [
                'slug' => WaybillPermission::getFullName(WaybillPermission::ALL),
            ],
            [
                'label'  => 'Adicionar permissão para visualização geral dos romaneios',
                'slug'   =>  WaybillPermission::getFullName(WaybillPermission::ALL),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

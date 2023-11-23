<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class PermissionViewPowerBiMcAfeeSeeder extends Seeder
{
    /** Run the database seeds. */
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => Permissions::DASHBOARD_MCAFEE_ALL,
            ],
            [
                'label'  => 'Adiciona permissão para visualizar o dashboard do mcafee por completo',
                'slug'   =>  Permissions::DASHBOARD_MCAFEE_ALL,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

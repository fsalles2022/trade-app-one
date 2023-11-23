<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;

class DashboardSalesPernambucanasPermissionSeeder extends Seeder
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
                'slug' => Permissions::DASHBOARD_PERNAMBUCANAS_SALES,
            ],
            [
                'label'  => 'Adiciona permissão ao dashboard Vendas Pernambucanas do powerBi',
                'slug'   => Permissions::DASHBOARD_PERNAMBUCANAS_SALES,
                'client' => SubSystemEnum::WEB,
            ]
        );
    }
}

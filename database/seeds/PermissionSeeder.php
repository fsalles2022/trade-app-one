<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\AnalyticalReportPermission;
use TradeAppOne\Domain\Enumerators\Permissions\BannerPermission;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardPermission;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Enumerators\Permissions\ManagementReportPermission;
use TradeAppOne\Domain\Enumerators\Permissions\ManualPermission;
use TradeAppOne\Domain\Enumerators\Permissions\NetworkPermission;
use TradeAppOne\Domain\Enumerators\Permissions\PointOfSalePermission;
use TradeAppOne\Domain\Enumerators\Permissions\RecoveryPermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\Permissions\UserPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Enumerators\Permissions\TriangulationPermission;

class PermissionSeeder extends Seeder
{
    private $clientsList = [
        SubSystemEnum::API => [
            NetworkPermission::class
        ],
        SubSystemEnum::WEB => [
            AnalyticalReportPermission::class,
            BannerPermission::class,
            DashboardPermission::class,
            ImportablePermission::class,
            ManagementReportPermission::class,
            ManualPermission::class,
            PointOfSalePermission::class,
            RecoveryPermission::class,
            SalePermission::class,
            UserPermission::class,
            TriangulationPermission::class
        ]
    ];

    public function run()
    {
        foreach ($this->clientsList as $client => $modules) {
            foreach ($modules as $module) {
                $permissions = (new $module)->getConstants();
                foreach ($permissions as $permission) {
                    factory(Permission::class)->create([
                        'label'  => trans(trans('permissions.' . $permission)),
                        'slug'   => $permission,
                        'client' => $client
                    ]);
                }
            }
        }
    }
}
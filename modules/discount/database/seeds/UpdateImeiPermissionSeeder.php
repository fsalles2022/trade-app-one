<?php

declare(strict_types=1);

namespace Discount\database\seeds;

use Discount\Enumerators\ImeiEnum;
use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class UpdateImeiPermissionSeeder extends Seeder
{
    public const VIEW            = 'VIEW';
    public const UPDATED         = 'UPDATE';
    public const TYPE_PERMISSION = [
        PermissionActions::VIEW => 'Visualizar o módulo para atualizar o IMEI',
        PermissionActions::EDIT => 'Permissão para atualizar IMEI em vendas'
    ];

    public function run(): void
    {
        foreach (self::TYPE_PERMISSION as $key => $permissionDescription) {
            Permission::updateOrCreate(
                [
                    'slug' => ImeiEnum::PERMISSION . '.' . $key
                ],
                [
                    'label'  => $permissionDescription,
                    'slug'   => ImeiEnum::PERMISSION . '.' . $key,
                    'client' => SubSystemEnum::WEB,
                ]
            );
        }
    }
}

<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class TimRebateImportablePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::getSlug(),
            ],
            [
                'label'  => 'Importação para carregar os descontos de aparelhos rebate TIM',
                'slug'   =>  self::getSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }

    public static function getSlug(): string
    {
        return ImportablePermission::NAME . '.' . ImportablePermission::TTM_REBATE;
    }
}

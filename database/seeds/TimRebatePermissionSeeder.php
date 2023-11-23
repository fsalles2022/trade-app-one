<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\TimRebatePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class TimRebatePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::getTimRebateUseSlug(),
            ],
            [
                'label'  => 'Permitir usar os descontos de aparelhos rebate TIM nas vendas TIM do varejo premium',
                'slug'   =>  self::getTimRebateUseSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }

    public static function getTimRebateUseSlug(): string
    {
        return TimRebatePermission::getFullName(TimRebatePermission::USE);
    }
}

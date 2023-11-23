<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class SingleRegistrationImportablePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            [
                'slug' => self::getSlug(),
            ],
            [
                'label'  => 'Importar cadastro único de usuários',
                'slug'   =>  self::getSlug(),
                'client' => SubSystemEnum::WEB,
            ]
        );
    }

    public static function getSlug(): string
    {
        return ImportablePermission::NAME . '.' . ImportablePermission::AUTOMATIC_REGISTRATION;
    }
}

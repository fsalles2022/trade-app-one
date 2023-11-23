<?php

use Illuminate\Database\Seeder;
use TradeAppOne\Domain\Enumerators\Permissions\BulletinPermissions;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;

class AddBulletinPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach(BulletinPermissions::DESCRIPTIONS as $slug => $label) {
            Permission::updateOrCreate(
                [
                    'slug' => $slug,
                ],
                [
                    'slug' => $slug,
                    'label' => $label,
                    'client' => SubSystemEnum::WEB
                ]
            );
        }
    }
}

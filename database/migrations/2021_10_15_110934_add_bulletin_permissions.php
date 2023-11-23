<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Permissions\BulletinPermissions;

class AddBulletinPermissions extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
           '--class' => AddBulletinPermissionsSeeder::class,
           '--force' => true
        ]);
    }

    public function down(): void
    {
        foreach (BulletinPermissions::DESCRIPTIONS as $slug => $description) {
            DB::unprepared("delete from permissions where slug = '" . $slug . "'");
        }
    }
}

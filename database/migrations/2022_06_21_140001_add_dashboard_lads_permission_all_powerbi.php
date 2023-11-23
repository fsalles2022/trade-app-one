<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Permissions\DashboardLadsPermission;

class AddDashboardLadsPermissionAllPowerbi extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => DashboardLadsPermissionAllSeeder::class,
            '--force' => true
        ]);
    }

    public function down():void
    {
        DB::unprepared('delete from permissions where slug = ?', [DashboardLadsPermission::getFullName(DashboardLadsPermission::VIEW_ALL)]);
    }
}

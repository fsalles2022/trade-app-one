<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Enumerators\Permissions;

class AddDashboardMcafeePermissionPowerbi extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'DashboardMcAfeePermissionSeeder',
            '--force' => true
        ]);
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        DB::unprepared("delete from permissions where slug = '" . Permissions::DASHBOARD_MCAFEE . "'");
    }
}

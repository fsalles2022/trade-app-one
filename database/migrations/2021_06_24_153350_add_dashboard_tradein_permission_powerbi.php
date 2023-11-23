<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Enumerators\Permissions;

class AddDashboardTradeInPermissionPowerbi extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        $seeder = new DashboardTradeInPermissionSeeder();
        $seeder->run();
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        DB::unprepared("delete from permissions where slug = '" . Permissions::DASHBOARD_TRADEIN . "'");
    }
}

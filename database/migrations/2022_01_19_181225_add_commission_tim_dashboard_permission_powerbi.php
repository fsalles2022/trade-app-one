<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Permissions;

class AddCommissionTimDashboardPermissionPowerbi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'DashboardCommissionTimPermissionSeeder',
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::unprepared("delete from permissions where slug = '" . Permissions::DASHBOARD_COMMISSION_TIM. "'");
    }
}

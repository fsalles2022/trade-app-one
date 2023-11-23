<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Permissions;

class AddPermissionViewPowerbiMcafee extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Artisan::call('db:seed', [
                '--class' => 'PermissionViewPowerBiMcAfeeSeeder',
                '--force' => true
        ]);
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        DB::unprepared('delete from permissions where slug = ?', [Permissions::DASHBOARD_MCAFEE_ALL]);
    }
}

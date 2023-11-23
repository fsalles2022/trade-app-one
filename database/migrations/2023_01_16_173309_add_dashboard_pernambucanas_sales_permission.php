<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use TradeAppOne\Domain\Enumerators\Permissions;

class AddDashboardPernambucanasSalesPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => 'DashboardSalesPernambucanasPermissionSeeder',
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("delete from permissions where slug = '" . Permissions::DASHBOARD_PERNAMBUCANAS_SALES. "'");
    }
}

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

class AddPermissionNewSaleFlowClaroV1 extends Migration
{
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => TradeHubSaleFlowPermissionsSeeder::class,
            '--force' => true
        ]);
    }

    public function down(): void
    {
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getPrePagoSlug());
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getControleFacilSlug());
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getResidentialSlug());
    }
}

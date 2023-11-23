<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

class AddPermissionNewSaleFlowTimV1 extends Migration
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
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getTimPrePagoSlug());
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getTimControleExpressSlug());
    }
}

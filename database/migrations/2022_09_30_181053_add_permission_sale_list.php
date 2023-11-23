<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

class AddPermissionSaleList extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Artisan::call('db:seed', [
            '--class' => TradeHubSaleFlowPermissionsSeeder::class,
            '--force' => true
        ]);
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getTradeHubSaleList());
        DB::unprepared('delete from permissions where slug = ' . TradeHubSaleFlowPermissionsSeeder::getTradeHubSaleAdministratorSlug());
    }
}

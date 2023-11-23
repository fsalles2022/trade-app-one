<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AddColumnIsPreSaleToDevicesNetworks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->boolean('isPreSale')->default(0);
        });

        $this->updateFieldIsPreSale();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->dropColumn('isPreSale');
        });

        DB::table('permissions')
            ->where('slug', 'SALE.CREATE_PRE_SALE')
            ->delete();
    }

    public function updateFieldIsPreSale(): void
    {
        Artisan::call('db:seed', [
            '--class' => 'UpdateIsPreSaleToDevicesNetwork',
            '--force' => true
        ]);

        Artisan::call('db:seed', [
            '--class' => 'AddPreSalePermission',
            '--force' => true
        ]);
    }
}

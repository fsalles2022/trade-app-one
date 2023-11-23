<?php

use Buyback\database\seeds\WatchOperationServiceSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddWatchOperationToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $seed = new WatchOperationServiceSeeder();
        $seed->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from services where operation = ?', ['WATCH']);
    }
}

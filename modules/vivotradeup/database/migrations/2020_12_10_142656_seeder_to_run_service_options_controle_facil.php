<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use VivoTradeUp\database\seeds\ServiceOptionsControleFacilM4uSeeder;

class SeederToRunServiceOptionsControleFacil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $seed = new ServiceOptionsControleFacilM4uSeeder();
        $seed->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from serviceOptions where action = ?', ['VIVO_CONTROLE_FACIL_M4U']);
    }
}

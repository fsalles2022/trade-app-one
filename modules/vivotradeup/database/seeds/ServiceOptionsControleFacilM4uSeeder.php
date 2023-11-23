<?php


namespace VivoTradeUp\database\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceOptionsControleFacilM4uSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('serviceOptions')->insert([
            'action' => 'VIVO_CONTROLE_FACIL_M4U'
        ]);
    }
}

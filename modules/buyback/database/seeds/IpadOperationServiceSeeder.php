<?php


namespace Buyback\database\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IpadOperationServiceSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('services')->insert([
            'sector' => 'TRADE_IN',
            'operator' => 'TRADE_IN_MOBILE',
            'operation' => 'IPLACE_IPAD',
            'label' => 'Trade In Ipad'
        ]);
    }
}

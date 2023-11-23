<?php

use Buyback\database\seeds\IpadOperationServiceSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddIplaceIpadService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $seed = new IpadOperationServiceSeeder();
        $seed->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::delete('delete from services where operation = ?', ['IPLACE_IPAD']);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompositeUniquePointsOfSaleUser extends Migration
{
    public function up()
    {
        Schema::table('pointsOfSale_users', function (Blueprint $table) {
            $table->unique(['pointsOfSaleId', 'userId'])->change();
        });
    }

    public function down()
    {
        Schema::table('pointsOfSale_users', function (Blueprint $table) {
            $table->dropIndex(['pointsOfSaleId', 'userId']);
        });
    }
}

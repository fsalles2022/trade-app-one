<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueSlugToPointsOfSaleTable extends Migration
{
    public function up()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->unique(['slug', 'networkId']);
        });
    }

    public function down()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->dropUnique('pointsofsale_slug_networkid_unique');
        });
    }
}

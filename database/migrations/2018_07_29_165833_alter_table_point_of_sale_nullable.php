<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePointOfSaleNullable extends Migration
{
    public function up()
    {
        Schema::table('pointsOfSale_users', function (Blueprint $table) {
            $table->unsignedInteger('pointsOfSaleId')->nullable(true)->change();
        });
    }

    public function down()
    {
        Schema::table('pointsOfSale_users', function (Blueprint $table) {
            $table->unsignedInteger('pointsOfSaleId')->nullable(false)->change();
        });
    }
}

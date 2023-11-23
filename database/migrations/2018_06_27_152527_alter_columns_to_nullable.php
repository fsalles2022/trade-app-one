<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsToNullable extends Migration
{
    public function up()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->string('companyName')->nullable()->default(null)->change();
            $table->string('areaCode')->nullable()->default(null)->change();
            $table->string('number')->nullable()->default(null)->change();
            $table->string('complement')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
    }
}

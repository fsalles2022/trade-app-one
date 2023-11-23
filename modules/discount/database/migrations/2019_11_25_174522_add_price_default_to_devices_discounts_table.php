<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceDefaultToDevicesDiscountsTable extends Migration
{
    public function up()
    {
        Schema::table('devices_discounts', function (Blueprint $table) {
            $table->string('price')->nullable(true)->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('devices_discounts', function (Blueprint $table) {
            $table->string('price')->nullable(false)->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuColumnToDevicesProductsTable extends Migration
{
    public function up()
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->string('sku')->nullable()->default(null);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sku');
    }
}

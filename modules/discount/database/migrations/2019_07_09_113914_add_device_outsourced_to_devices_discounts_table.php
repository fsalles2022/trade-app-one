<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceOutsourcedToDevicesDiscountsTable extends Migration
{
    public function up()
    {
        Schema::table('devices_discounts', function (Blueprint $table) {
            $table->dropForeign(['deviceId']);

            $table->foreign('deviceId')
                ->references('id')
                ->on('devices_outsourced')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('devices_discounts', function (Blueprint $table) {
            $table->dropForeign(['deviceId']);

            $table->foreign('deviceId')
                ->references('id')
                ->on('devices')
                ->onDelete('cascade');
        });
    }
}

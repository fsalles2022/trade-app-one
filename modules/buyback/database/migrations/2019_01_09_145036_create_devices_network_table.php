<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesNetworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices_network', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deviceId');
            $table->foreign('deviceId')->references('id')->on('devices')->onDelete('cascade');
            $table->unsignedInteger('networkId');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');

            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_network');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('devices_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->double('price', 8, 2);
            $table->double('discount', 8, 2);
            $table->unsignedInteger('deviceId');
            $table->unsignedInteger('discountId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
            $table->foreign('deviceId')->references('id')->on('devices')->onDelete('cascade');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices_discounts');
    }
}

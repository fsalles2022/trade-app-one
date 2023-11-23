<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesOutsourcedTable extends Migration
{
    public function up()
    {
        Schema::create('devices_outsourced', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku');
            $table->string('model');
            $table->string('label');
            $table->string('brand')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->string('storage')->nullable()->default(null);
            $table->unsignedInteger('networkId');

            $table->unique(['sku', 'networkId']);

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());;
            $table->timestamp('updatedAt')->default(now());;

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices_outsourced');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model');
            $table->string('brand');
            $table->string('color');
            $table->string('storage');
            $table->string('imageFront');
            $table->string('imageBehind');

            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTierTable extends Migration
{
    public function up()
    {
        Schema::create('deviceTier', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('goodTierNote');
            $table->integer('middleTierNote');
            $table->integer('defectTierNote');
            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function down()
    {
        Schema::dropIfExists('deviceTier');
    }
}

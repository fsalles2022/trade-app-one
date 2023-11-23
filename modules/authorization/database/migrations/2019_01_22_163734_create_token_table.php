<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokenTable extends Migration
{
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('accessKey');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tokens');
    }
}

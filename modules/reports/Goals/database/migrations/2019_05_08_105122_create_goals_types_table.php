<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoalsTypesTable extends Migration
{
    public function up()
    {
        Schema::create('goalsTypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->unique();
            $table->string('label');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('goalsTypes');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        Schema::create('passwordResets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token')->nullable(false);
            $table->string('status')->nullable(false)->default('WAITING');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('userId');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS    = 0');
        Schema::drop('passwordResets');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
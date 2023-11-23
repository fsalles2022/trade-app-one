<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableHierarchiesUsersTable extends Migration
{
    public function up()
    {
        Schema::create('hierarchies_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hierarchyId');
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('hierarchies_users');
    }
}

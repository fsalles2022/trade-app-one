<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperatorsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operators_users', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('operatorId');
            $table->unsignedInteger('userId');

            $table->foreign('operatorId')->references('id')->on('operators')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operators_users', function (Blueprint $table) {
            $table->dropForeign('operatorId');
            $table->dropForeign('userId');
        });
        Schema::dropIfExists('operators_users');
    }
}

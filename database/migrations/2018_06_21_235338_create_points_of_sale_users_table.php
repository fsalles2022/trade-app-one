<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsOfSaleUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pointsOfSale_users', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('pointsOfSaleId');
            $table->unsignedInteger('userId');

            $table->foreign('pointsOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
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
        Schema::table('pointsOfSale_users', function (Blueprint $table) {
            $table->dropForeign('pointsOfSaleId');
            $table->dropColumn('pointsOfSaleId');

            $table->dropForeign('userId');
            $table->dropColumn('userId');
        });
        Schema::dropIfExists('pointsOfSale_users');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GoalsTable extends Migration
{
    const TABLE = 'goals';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('pointOfSaleId');
            $table->integer('year')->nullable(false);
            $table->integer('month')->nullable(false);
            $table->integer('goal')->nullable(false);

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');

            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(self::TABLE);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

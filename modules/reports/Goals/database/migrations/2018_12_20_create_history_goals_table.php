<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HistoryGoalsTable extends Migration
{
    const TABLE = 'historyGoals';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('userId');
            $table->integer('file')->unique();
            $table->enum('result', ['SUCCESS', 'ERROR', 'PARTIAL_SUCCESS'])->unique();

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists(self::TABLE);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

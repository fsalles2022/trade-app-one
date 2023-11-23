<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('evaluations');
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quizId');
            $table->foreign('quizId')->references('id')->on('quizzes')->onDelete('cascade');
            $table->unsignedInteger('deviceNetworkId');
            $table->foreign('deviceNetworkId')->references('id')->on('devices_network')->onDelete('cascade');
            $table->float('goodValue');
            $table->float('averageValue');
            $table->float('defectValue');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
}

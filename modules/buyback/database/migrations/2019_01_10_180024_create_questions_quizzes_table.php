<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create('questions_quizzes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('questionId');
            $table->foreign('questionId')->references('id')->on('questions')->onDelete('cascade');
            $table->unsignedInteger('quizId');
            $table->foreign('quizId')->references('id')->on('quizzes')->onDelete('cascade');
            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions_quizzes');
    }
}

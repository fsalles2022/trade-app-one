<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->string('weight');
            $table->string('order');
            $table->boolean('blocker');
            $table->unsignedInteger('networkId');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}

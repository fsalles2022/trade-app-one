<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('importHistory', function (Blueprint $table) {
            $table->increments('id');
            $table->text('type');
            $table->text('inputFile')->nullable(true);
            $table->text('outputFile')->nullable(true);
            $table->text('status');
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('importHistory');
    }
}

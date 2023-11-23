<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHandbooksTable extends Migration
{
    public function up()
    {
        Schema::create('handbooks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->string('title');
            $table->string('description')->nullable()->default(null);
            $table->string('file');
            $table->string('type');
            $table->string('module');
            $table->string('category');
            $table->string('networksFilterMode')->nullable()->default(null);
            $table->string('rolesFilterMode')->nullable()->default(null);
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('handbooks');
    }
}

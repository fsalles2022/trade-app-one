<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceOptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('serviceOptions', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('action');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serviceOptions');
    }
}

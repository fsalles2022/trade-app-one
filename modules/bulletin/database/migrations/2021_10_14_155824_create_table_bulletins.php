<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBulletins extends Migration
{
    /** @return void */
    public function up(): void
    {
        Schema::create('bulletins', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('networkId');
            $table->boolean('status')->nullable(false);
            $table->string('urlImage');
            $table->dateTime('initialDate');
            $table->dateTime('finalDate');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    /** @return void */
    public function down(): void
    {
        Schema::dropIfExists('bulletins');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinsUsersTable extends Migration
{
    /*** @return void */
    public function up(): void
    {
        Schema::create('bulletins_users', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('bulletinId');
            $table->boolean('seen')->default(0)->nullable(false);
            $table->unsignedInteger('userId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /*** @return void */
    public function down(): void
    {
        Schema::dropIfExists('bulletins_users');
    }
}

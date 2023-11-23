<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinsRolesTable extends Migration
{
    /*** @return void */
    public function up(): void
    {
        Schema::create('bulletins_roles', static function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('bulletinId');
            $table->unsignedInteger('roleId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /*** @return void */
    public function down(): void
    {
        Schema::dropIfExists('bulletins_roles');
    }
}

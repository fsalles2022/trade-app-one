<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('roleId');
            $table->unsignedInteger('permissionsId');

            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permissionsId')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('role_permissions');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

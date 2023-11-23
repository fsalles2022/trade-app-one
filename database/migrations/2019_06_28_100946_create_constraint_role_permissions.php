<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConstraintRolePermissions extends Migration
{
    public function up()
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->unique(['roleId', 'permissionsId']);
        });
    }

    public function down()
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropUnique(['roleId', 'permissionsId']);
        });
    }
}

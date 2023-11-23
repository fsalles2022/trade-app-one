<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsConfigurationTable extends Migration
{
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['roleId']);
            $table->dropColumn(['roleId']);
            $table->string('client')->nullable(false);

            $table->unique(['client', 'slug']);
        });
    }


    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedInteger('roleId')->nullable();
            $table->dropUnique(['client', 'slug']);
            $table->dropColumn(['client']);
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }
}

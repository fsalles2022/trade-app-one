<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnParentAndSequenceToRolesTable extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('sequence');
            $table->unsignedInteger('parent')->nullable()->null();
            $table->foreign('parent')->references('id')->on('roles')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent']);
            $table->dropColumn('sequence');
            $table->dropColumn('parent');
        });
    }
}

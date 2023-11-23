<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHandbooksRolesTable extends Migration
{
    public function up()
    {
        Schema::create('handbooks_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('handbookId');
            $table->unsignedInteger('roleId');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->foreign('handbookId')->references('id')->on('handbooks')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('handbooks_roles');
    }
}

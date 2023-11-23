<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerIdToPasswordResets extends Migration
{
    public function up()
    {
        Schema::table('passwordResets', function (Blueprint $table) {
            $table->unsignedInteger('managerId')->nullable(true);
            $table->foreign('managerId')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('passwordResets', function (Blueprint $table) {
            $table->dropForeign(['managerId']);
            $table->dropColumn('managerId');
        });
    }
}

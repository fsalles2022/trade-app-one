<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnPasswordToNullableOnUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table){
            $table->string('password')->nullable(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table){
            $table->string('password')->nullable(false)->change();
        });
    }
}

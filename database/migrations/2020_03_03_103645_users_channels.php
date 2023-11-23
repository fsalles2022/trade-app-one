<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersChannels extends Migration
{
    public function up(): void
    {
        Schema::create('users_channels', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->unsignedInteger('channelId');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('channelId')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_channels');
    }
}
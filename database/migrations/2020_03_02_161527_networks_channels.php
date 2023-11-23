<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NetworksChannels extends Migration
{
    public function up(): void
    {
        Schema::create('networks_channels', static function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('networkId');
            $table->unsignedInteger('channelId');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->foreign('channelId')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('networks_channels');
    }
}
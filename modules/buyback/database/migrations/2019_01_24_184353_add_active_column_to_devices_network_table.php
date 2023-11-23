<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveColumnToDevicesNetworkTable extends Migration
{
    public function up()
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->boolean('active');
        });
    }

    public function down()
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->dropColumn(['active']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueDeviceIdAndNetworkIdToDevicesNetworkTable extends Migration
{
    public function up()
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->unique(['deviceId', 'networkId']);
        });
    }

    public function down()
    {
        Schema::table('devices_network', function (Blueprint $table) {
            $table->dropUnique('devices_network_deviceid_networkid_unique');
        });
    }
}

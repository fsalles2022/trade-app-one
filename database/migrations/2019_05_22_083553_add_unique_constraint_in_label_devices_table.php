<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintInLabelDevicesTable extends Migration
{
    public function up()
    {
        Schema::table('devices', function (Blueprint $table){
            $table->unique('label');
        });
    }

    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique('label');
        });
    }
}

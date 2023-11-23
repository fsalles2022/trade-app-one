<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnChannelToNetworkTable extends Migration
{
    public function up()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('responsiblePersons');
            $table->string('channel')->nullable(true);
        });
    }

    public function down()
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->string('responsiblePersons')->nullable(true);
            $table->dropColumn('channel');
        });
    }
}

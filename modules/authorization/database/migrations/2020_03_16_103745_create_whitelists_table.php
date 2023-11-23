<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhitelistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->create('whitelists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->unsignedInteger('integrationId');
            $table->unique(['ip', 'integrationId']);

            $table->foreign('integrationId')->references('id')->on('integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whitelists');
    }
}

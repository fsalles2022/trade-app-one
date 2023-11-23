<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationsRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->create('integrations_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('integrationId');
            $table->unsignedInteger('routeId');
            $table->unique(['integrationId', 'routeId']);

            $table->foreign('integrationId')->references('id')->on('integrations');
            $table->foreign('routeId')->references('id')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('integrations_routes');
    }
}

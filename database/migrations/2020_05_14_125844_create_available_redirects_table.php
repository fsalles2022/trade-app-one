<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->create('available_redirects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('integrationId');
            $table->string('redirectUrl');
            $table->boolean('defaultUrl')->default(true);
            $table->string('routeKey');

            $table->foreign('integrationId')->references('id')->on('integrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('available_redirects');
    }
}

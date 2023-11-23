<?php

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->create('integrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accessKey');
            $table->unsignedInteger('networkId');
            $table->unsignedInteger('operatorId');
            $table->unsignedInteger('userId');

            $db = DB::connection('mysql')->getDatabaseName();

            $table->foreign('networkId')->references('id')->on((new Expression($db . '.networks'))->__toString());
            $table->foreign('operatorId')->references('id')->on((new Expression($db . '.operators'))->__toString());
            $table->foreign('userId')->references('id')->on((new Expression($db . '.users'))->__toString());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('integrations');
    }
}

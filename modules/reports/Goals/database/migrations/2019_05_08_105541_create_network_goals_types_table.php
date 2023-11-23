<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkGoalsTypesTable extends Migration
{
    public function up()
    {
        Schema::create('network_goalsTypes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('networkId');
            $table->unsignedInteger('goalTypeId');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
            $table->foreign('goalTypeId')->references('id')->on('goalsTypes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('network_goalsTypes');
    }
}

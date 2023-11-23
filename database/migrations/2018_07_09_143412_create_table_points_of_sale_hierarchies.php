<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePointsOfSaleHierarchies extends Migration
{
    public function up()
    {
        Schema::create('pointsOfSale_hierarchies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pointOfSaleId');
            $table->unsignedInteger('hierarchyId');

            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');

            $table->unique(['pointOfSaleId', 'hierarchyId']);
        });
    }

    public function down()
    {
        Schema::drop('pointsOfSale_hierarchies');
    }
}

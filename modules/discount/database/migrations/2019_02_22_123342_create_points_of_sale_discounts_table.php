<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsOfSaleDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('pointsOfSale_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pointOfSaleId');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->unsignedInteger('discountId');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pointsOfSale_discounts');
    }
}

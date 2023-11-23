<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableServicesTable extends Migration
{
    public function up()
    {
        Schema::create('availableServices', function (Blueprint $table): void {
            $table->increments('id');

            $table->unsignedInteger('serviceId');
            $table->unsignedInteger('pointOfSaleId')->nullable(true);
            $table->unsignedInteger('networkId')->nullable(true);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('serviceId')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('availableServices');
    }
}

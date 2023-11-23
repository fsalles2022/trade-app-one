<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesServiceOptions extends Migration
{
    public function up()
    {
        Schema::create('services_serviceOptions', function (Blueprint $table) {
            $table->increments('id');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->unsignedInteger('availableServiceId');
            $table->unsignedInteger('optionId');

            $table->foreign('availableServiceId')->references('id')->on('availableServices')->onDelete('cascade');
            $table->foreign('optionId')->references('id')->on('serviceOptions')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::dropIfExists('servicePreferences');
    }
}

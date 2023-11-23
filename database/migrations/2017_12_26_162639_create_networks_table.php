<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetworksTable extends Migration
{
    public function up()
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('label')->nullable()->default(null);
            $table->string('cnpj')->unique();
            $table->string('tradingName')->nullable()->default(null);
            $table->string('companyName')->nullable()->default(null);
            $table->string('telephone')->nullable()->default(null);
            $table->string('state')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('zipCode')->nullable()->default(null);
            $table->string('local')->nullable()->default(null);
            $table->string('neighborhood')->nullable()->default(null);
            $table->integer('number')->nullable()->default(null);
            $table->string('complement')->nullable()->default(null);

            $table->json('preferences')->nullable()->default(null);
            $table->json('availableServices')->nullable()->default(null);
            $table->json('responsiblePersons')->nullable()->default(null);

            $table->softDeletes('deletedAt');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });
    }

    public function down()
    {
        Schema::drop('networks');
    }
}

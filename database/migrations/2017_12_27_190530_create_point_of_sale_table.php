<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointOfSaleTable extends Migration
{
    public function up()
    {
        Schema::create('pointsOfSale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable();
            $table->string('label')->nullable()->default(null);
            $table->string('cnpj')->unique();
            $table->string('tradingName')->nullable()->default(null);
            $table->string('companyName');
            $table->string('areaCode');
            $table->string('telephone')->nullable()->default(null);
            $table->string('state')->nullable()->default(null);
            $table->string('city')->nullable()->default(null);
            $table->string('zipCode')->nullable()->default(null);
            $table->string('local')->nullable()->default(null);
            $table->string('neighborhood')->nullable()->default(null);
            $table->integer('number');
            $table->string('complement');
            $table->unsignedInteger('networkId');
            $table->float('latitude', 10, 7)->nullable()->default(0.0);
            $table->float('longitude', 11, 7)->nullable()->default(0.0);

            $table->json('integrationIdentifiers')->nullable()->default(null);
            $table->json('providerIdentifiers')->nullable()->default(null);
            $table->json('availableServices')->nullable()->default(null);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->dropForeign('networkId');
            $table->dropColumn('networkId');
        });
        Schema::drop('pointsOfSale');
    }
}

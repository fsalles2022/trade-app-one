<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('statusCode')->default('ACTIVE');
            $table->string('registration');
            $table->unsignedInteger('pointOfSaleId')->nullable();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropForeign('pointOfSaleId');
            $table->dropColumn('pointOfSaleId');
        });
        Schema::dropIfExists('recommendations');
    }
}

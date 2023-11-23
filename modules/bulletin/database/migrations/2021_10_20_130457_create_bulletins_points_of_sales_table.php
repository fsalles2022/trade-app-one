<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinsPointsOfSalesTable extends Migration
{
    /** @return void */
    public function up(): void
    {
        Schema::create('bulletins_pointsOfSales', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('bulletinId')->nullable(false);
            $table->unsignedInteger('pointOfSaleId')->nullable(false);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('bulletinId')->references('id')->on('bulletins')->onDelete('cascade');
            $table->foreign('pointOfSaleId')->references('id')->on('pointsOfSale')->onDelete('cascade');
        });
    }

    /** @return void */
    public function down(): void
    {
        Schema::dropIfExists('bulletins_pointsOfSales');
    }
}

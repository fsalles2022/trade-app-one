<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneraliProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::connection('outsourced')->create('generali_products', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable(false);
            $table->double('startingTrack', 8, 2)->nullable(false);
            $table->double('finalTrack', 8, 2)->nullable(false);
            $table->integer('twelveMonthsCode')->unique()->nullable(false);
            $table->integer('twentyFourMonthsCode')->unique()->nullable(false);
            $table->double('twelveMonthsPrice', 8, 2)->nullable(false);
            $table->double('twentyFourMonthsPrice', 8, 2)->nullable(false);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('generali_products');
    }
}

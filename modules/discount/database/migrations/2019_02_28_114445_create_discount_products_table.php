<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountProductsTable extends Migration
{
    public function up()
    {
        Schema::create('discount_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('operator')->nullable(true)->default(null);
            $table->string('operation')->nullable(true)->default(null);
            $table->string('product')->nullable(true)->default(null);

            $table->string('filterMode')->nullable(true)->default(null);

            $table->string('label')->nullable();

            $table->unsignedInteger('discountId');
            $table->foreign('discountId')->references('id')->on('discounts')->onDelete('cascade');

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_products');
    }
}

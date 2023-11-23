<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionToDiscountProductsTable extends Migration
{
    public function up()
    {
        Schema::table('discount_products', function (Blueprint $table) {
            $table->string('promotion')->nullable(true)->default(null);
        });
    }

    public function down()
    {
        Schema::table('discount_products', function (Blueprint $table) {
            $table->dropColumn('promotion');
        });
    }
}

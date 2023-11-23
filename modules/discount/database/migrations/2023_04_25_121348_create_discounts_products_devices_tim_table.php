<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDiscountsProductsDevicesTimTable extends Migration
{
    public function up(): void
    {
        Schema::create('discounts_products_devices_tim', function (Blueprint $table): void {
            $table->increments('id');
            $table->double('discount', 8, 2);
            $table->unsignedInteger('discountProductId');
            $table->unsignedInteger('deviceId');
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('discountProductId')->references('id')->on('discount_products_tim')->onDelete('cascade');
            $table->foreign('deviceId')->references('id')->on('devices_outsourced_tim')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('discounts_products_devices_tim', function (Blueprint $table): void {
            $table->dropForeign(['discountProductId']);
            $table->dropForeign(['deviceId']);
        });

        Schema::dropIfExists('discounts_products_devices_tim');
    }
}

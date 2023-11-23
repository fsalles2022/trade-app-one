<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDiscountProductsTimTable extends Migration
{
    public function up(): void
    {
        Schema::create('discount_products_tim', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('label');
            $table->string('externalIdentifier', 20);
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->index(['externalIdentifier']);
        });
    }

    public function down(): void
    {
        Schema::table('discount_products_tim', function (Blueprint $table): void {
            $table->dropIndex(['externalIdentifier']);
        });

        Schema::dropIfExists('discount_products_tim');
    }
}

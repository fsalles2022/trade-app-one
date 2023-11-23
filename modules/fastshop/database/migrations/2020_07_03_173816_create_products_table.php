<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // TODO refatorar para um seeder.
        $productsJson = json_decode(Storage::get('dummyData/products.json'));

        $rawProducts = $productsJson->products;

        $productNormalize = resolve('FastShop\Services\ProductNormalize');

        $normalizedProducts = $productNormalize->normalizeAll($rawProducts);

        $bulk = [];

        foreach ($normalizedProducts as $normalizedProduct) {
            $bulk[] = $normalizedProduct->toArray();
        }

        Schema::create('products', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('title');
            $table->unsignedTinyInteger('areaCode');
            $table->unsignedTinyInteger('loyaltyMonths');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('internet')->default(0);
            $table->unsignedInteger('minutes')->default(0);

            $table->unsignedInteger('serviceId');

            $table->json('extras')->nullable();
            $table->json('original')->nullable();

            $table->index('code');
            $table->index('areaCode');

            $table->foreign('serviceId')->references('id')->on('services');

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
        });

        if (Schema::hasTable('products')) {
            DB::table('products')->insert($bulk);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}

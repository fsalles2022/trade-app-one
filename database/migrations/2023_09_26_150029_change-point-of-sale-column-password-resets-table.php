<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePointOfSaleColumnPasswordResetsTable extends Migration
{
    public function up(): void
    {
        Schema::table('passwordResets', function (Blueprint $table) {
            $table->dropForeign(['pointsOfSaleId']);
        });

        Schema::table('passwordResets', function (Blueprint $table) {
            $table->unsignedInteger('pointsOfSaleId')->nullable(true)->default(null)->change();

            $table->foreign('pointsOfSaleId')->references('id')->on('pointsOfSale');
        });
    }

    public function down(): void
    {
        Schema::table('passwordResets', function (Blueprint $table) {
            $table->dropForeign(['pointsOfSaleId']);
        });

        Schema::table('passwordResets', function (Blueprint $table) {
            $table->unsignedInteger('pointsOfSaleId')->nullable(false)->change();

            $table->foreign('pointsOfSaleId')->references('id')->on('pointsOfSale');
        });
    }
}

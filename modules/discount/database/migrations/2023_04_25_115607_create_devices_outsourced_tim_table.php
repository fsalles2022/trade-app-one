<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDevicesOutsourcedTimTable extends Migration
{
    public function up(): void
    {
        Schema::create('devices_outsourced_tim', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('label');
            $table->string('model');
            $table->string('brand', 50)->nullable()->default(null);
            $table->double('price', 8, 2);
            $table->string('externalIdentifier', 15)->nullable()->default(null);
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->index(['label']);
            $table->index(['model']);
            $table->index(['brand']);
            $table->index(['externalIdentifier']);
        });
    }

    public function down(): void
    {
        Schema::table('devices_outsourced_tim', function (Blueprint $table): void {
            $table->dropIndex(['label']);
            $table->dropIndex(['model']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['externalIdentifier']);
        });

        Schema::dropIfExists('devices_outsourced_tim');
    }
}

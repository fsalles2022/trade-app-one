<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViaVarejoCuponsTable extends Migration
{
    public function up(): void
    {
        Schema::connection('outsourced')->create('via_varejo_coupons', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon')->unique();
            $table->string('campaign');
            $table->integer('discountId')->unique();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('via_varejo_cupons');
    }
}

<?php

use Discount\Enumerators\DiscountStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Discount\Enumerators\DiscountModes;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {

            $table->increments('id');
            $table->string('title');
            $table->string('status')->default(DiscountStatus::ACTIVE);

            $table->string('filterMode')->default(DiscountModes::ALL);
            $table->dateTime('startAt')->nullable()->default(null);
            $table->dateTime('endAt')->nullable()->default(null);
            $table->unsignedInteger('userId')->nullable(true)->default(null);
            $table->unsignedInteger('networkId');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}

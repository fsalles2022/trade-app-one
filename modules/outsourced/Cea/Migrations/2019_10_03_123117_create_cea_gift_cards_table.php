<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCeaGiftCardsTable extends Migration
{
    public function up()
    {
        Schema::connection('outsourced')->create('cea_gift_cards', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('partner')->nullable();
            $table->float('value')->nullable();
            $table->string('outsourcedId')->nullable();
            $table->string('reference')->nullable();

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cea_gift_cards');
    }
}

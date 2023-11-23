<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandbooksNetworksTable extends Migration
{
    public function up()
    {
        Schema::create('handbooks_networks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('handbookId');
            $table->unsignedInteger('networkId');

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->foreign('handbookId')->references('id')->on('handbooks')->onDelete('cascade');
            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('handbooks_networks');
    }
}

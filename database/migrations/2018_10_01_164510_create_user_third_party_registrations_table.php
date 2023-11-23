<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserThirdPartyRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('userThirdPartyRegistrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('operator')->nullable(false);
            $table->string('log')->nullable(true);
            $table->boolean('done')->default(false);


            $table->unsignedInteger('userId');
            $table->unsignedInteger('pointOfSaleId')->nullable(true);

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pointOfSaleId', 'foreign_currentpointofsaleid')
                ->references('id')
                ->on('pointsOfSale')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('userThirdPartyRegistrations');
    }
}

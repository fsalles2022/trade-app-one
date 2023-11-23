<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationsBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('evaluations_bonus', static function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('evaluationId');
            $table->double('bonusValue', 8, 2);
            $table->string('sponsor');

            $table->softDeletes('deletedAt');
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->foreign('evaluationId')->references('id')->on('evaluations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations_bonus');
    }
}

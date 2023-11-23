<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsGoodAvaregeDefectInEvaluationsBonusTable extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations_bonus', function (Blueprint $table) {
            $table->double('goodValue', 8, 2)->default(0.00);
            $table->double('averageValue', 8, 2)->default(0.00);
            $table->double('defectValue', 8, 2)->default(0.00);
            $table->dropColumn('bonusValue');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations_bonus', function (Blueprint $table) {
            $table->dropColumn('goodValue');
            $table->dropColumn('averageValue');
            $table->dropColumn('defectValue');
            $table->double('bonusValue', 8, 2)->default(0.00);
        });
    }
}

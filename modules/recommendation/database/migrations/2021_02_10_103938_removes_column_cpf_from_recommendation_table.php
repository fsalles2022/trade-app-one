<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovesColumnCpfFromRecommendationTable extends Migration
{
    public function up(): void
    {
        Schema::table('recommendations', static function (Blueprint $table) {
            $table->dropColumn('cpf');
            $table->dropUnique('recommendations_registration_unique');
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', static function (Blueprint $table) {
            $table->string('cpf')->unique();
        });
    }
}

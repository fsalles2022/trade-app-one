<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoalTypeIdColumnGoalsTable extends Migration
{
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->unsignedInteger('goalTypeId')->nullable();
            $table->foreign('goalTypeId')->references('id')->on('goalsTypes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('goalTypeId');
        });
    }
}

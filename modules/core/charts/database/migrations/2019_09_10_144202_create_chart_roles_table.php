<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dashboards_charts');

        Schema::create('chart_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('size');
            $table->integer('order');
            $table->unsignedInteger('chartId');
            $table->unsignedInteger('roleId');
            $table->timestamp('updatedAt')->default(now());
            $table->timestamp('createdAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('chartId')->references('id')->on('charts')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_roles');

        Schema::create('dashboards_charts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('size');
            $table->unsignedInteger('chartId');
            $table->unsignedInteger('roleId');
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');

            $table->foreign('chartId')->references('id')->on('charts')->onDelete('cascade');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }
}

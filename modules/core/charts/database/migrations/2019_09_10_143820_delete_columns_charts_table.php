<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnsChartsTable extends Migration
{
    public function up()
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->dropColumn('order');
            $table->dropColumn('render');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('type');
        });
    }

    public function down()
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('type');
            $table->string('size');
            $table->string('order');
            $table->string('render');
        });
    }
}

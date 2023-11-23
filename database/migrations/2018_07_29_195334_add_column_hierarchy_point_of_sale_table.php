<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnHierarchyPointOfSaleTable extends Migration
{
    public function up()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->unsignedInteger('hierarchyId')->nullable(true);
            $table->foreign('hierarchyId')->references('id')->on('hierarchies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('pointsOfSale', function (Blueprint $table) {
            $table->dropForeign(['hierarchyId']);
            $table->dropColumn('hierarchyId');
        });
    }
}

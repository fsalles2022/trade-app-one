<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHierarchyAndCustomertypeInImportedSales extends Migration
{
    public function up()
    {
        Schema::table('importSales', function (Blueprint $table) {
            $table->string('pointofsale_hierarchy_label')->nullable();
            $table->string('customertype')->nullable();
        });
    }

    public function down()
    {
        Schema::table('importSales', function (Blueprint $table) {
            $table->dropColumn('pointofsale_hierarchy_label');
            $table->dropColumn('customertype');
        });
    }
}

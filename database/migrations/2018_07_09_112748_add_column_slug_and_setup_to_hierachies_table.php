<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSlugAndSetupToHierachiesTable extends Migration
{
    public function up()
    {
        Schema::table('hierarchies', function (Blueprint $table) {
            $table->string('slug')->nullable(true);
            $table->unsignedInteger('networkId')->nullable(true);
            $table->renameColumn('nome', 'label');
            $table->unique('slug');

            $table->foreign('networkId')->references('id')->on('networks')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('hierarchies', function (Blueprint $table) {
            $table->dropUnique('slug');
            $table->renameColumn('label', 'nome');
        });
    }
}

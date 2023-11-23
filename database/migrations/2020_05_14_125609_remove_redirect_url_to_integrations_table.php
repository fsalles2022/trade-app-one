<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveRedirectUrlToIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->table('integrations', function (Blueprint $table) {
            $table->dropColumn('redirectUrl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('outsourced')->table('integrations', function (Blueprint $table) {
            $table->string('redirectUrl', 255)->nullable();
        });
    }
}

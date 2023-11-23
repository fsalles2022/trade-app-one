<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthPartnerColumnsToIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('outsourced')->table('integrations', function (Blueprint $table) {
            $table->unsignedInteger('networkId')->nullable()->change();
            $table->unsignedInteger('operatorId')->nullable()->change();
            $table->unsignedInteger('userId')->nullable()->change();
            $table->string('credentialVerifyUrl', 255)->nullable();
            $table->string('redirectUrl', 255)->nullable();
            $table->string('subdomain', 100)->nullable();
            $table->string('client', 100)->nullable();
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
            $table->unsignedInteger('networkId')->change();
            $table->unsignedInteger('operatorId')->change();
            $table->unsignedInteger('userId')->change();
            $table->dropColumn('credentialVerifyUrl');
            $table->dropColumn('redirectUrl');
            $table->dropColumn('subdomain');
            $table->dropColumn('client');
        });
    }
}

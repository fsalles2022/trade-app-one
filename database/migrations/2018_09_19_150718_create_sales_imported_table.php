<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSalesImportedTable extends Migration
{
    public function up()
    {
        Schema::create('importSales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source')->nullable();
            $table->string('pointofsale_id')->nullable();
            $table->string('saletransaction');
            $table->string('pointofsale_cnpj')->nullable();
            $table->string('pointofsale_network_slug')->nullable();
            $table->string('pointofsale_network_label')->nullable();
            $table->string('pointofsale_hierarchy')->nullable();
            $table->string('pointofsale_state')->nullable();
            $table->string('pointofsale_areaCode')->nullable();
            $table->string('pointofsale_label')->nullable();
            $table->string('user_id')->nullable();
            $table->string('user_cpf')->nullable();
            $table->string('user_role')->nullable();
            $table->string('user_firstname')->nullable();
            $table->string('service_servicetransaction')->unique();
            $table->string('service_operator')->nullable();
            $table->string('service_sector')->nullable();
            $table->string('service_operation')->nullable();
            $table->string('service_mode')->nullable();
            $table->string('service_product')->nullable();
            $table->string('service_label')->nullable();
            $table->string('service_iccid')->nullable();
            $table->string('service_msisdn')->nullable();
            $table->string('service_portednumber')->nullable();
            $table->string('service_dueDate')->nullable();
            $table->string('service_statusthirdparty')->nullable();
            $table->string('service_status')->nullable();
            $table->string('service_invoicetype')->nullable();
            $table->string('service_operator_pid');
            $table->string('service_operator_sid')->nullable();
            $table->string('service_customer_cpf')->nullable();
            $table->string('service_customer_firstname')->nullable();
            $table->string('service_customer_lastname')->nullable();
            $table->string('service_customer_birthday')->nullable();
            $table->string('service_customer_gender')->nullable();
            $table->string('service_customer_filiation')->nullable();
            $table->string('service_customer_email')->nullable();
            $table->string('service_customer_mainPhone')->nullable();
            $table->string('service_customer_secondaryphone')->nullable();
            $table->string('service_customer_zipcode')->nullable();
            $table->string('service_customer_local')->nullable();
            $table->string('service_customer_number')->nullable();
            $table->string('service_customer_neighborhood')->nullable();
            $table->string('service_customer_state')->nullable();
            $table->string('service_customer_city')->nullable();
            $table->double('service_price')->nullable()->default(null);
            $table->string('service_operatoridentifiers_acceptance')->nullable()->default(null);

            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');
            $table->unique(['service_operator', 'service_operator_pid', 'service_operator_sid'], 'operator_pid_sid');
        });
    }

    public function down()
    {
        Schema::drop('importSales');
    }
}

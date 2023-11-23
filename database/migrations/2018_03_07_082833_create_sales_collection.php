<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesCollection extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('sales', function (Blueprint $collection) {
            $collection->index('user.cpf');
            $collection->index('customer.cpf');
            $collection->index('saleTransaction');
            $collection->index('services.serviceTransaction');
            $collection->softDeletes();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->drop('sales');
    }
}

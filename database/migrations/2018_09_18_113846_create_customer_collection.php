<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerCollection extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('customers', function (Blueprint $collection) {
            $collection->index('cpf');
            $collection->softDeletes();
        });
    }

    public function down()
    {
        Schema::connection('mongodb')->drop('customers');
    }
}

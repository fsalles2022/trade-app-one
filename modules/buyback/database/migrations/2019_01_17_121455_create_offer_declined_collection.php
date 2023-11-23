<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferDeclinedCollection extends Migration
{
    public function up()
    {
        Schema::connection('mongodb')->create('offerDeclined', function (Blueprint $collection) {
            $collection->index('device.imei');
            $collection->softDeletes();
        });
    }

    public function down()
    {
        //
    }
}

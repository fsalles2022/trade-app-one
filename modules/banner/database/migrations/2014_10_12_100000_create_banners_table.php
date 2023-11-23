<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('key');
            $table->string('href')->nullable(true);
            $table->string('reference')->nullable(true);
            $table->unsignedInteger('order')->default(0);
            $table->string('image_desktop')->nullable(true);
            $table->string('image_tablet')->nullable(true);
            $table->string('image_mobile')->nullable(true);

            $table->string('image_desktop_placeholder')->nullable(true);
            $table->string('image_tablet_placeholder')->nullable(true);
            $table->string('image_mobile_placeholder')->nullable(true);

            $table->dateTime('start_at')->nullable(true)->default(null);
            $table->dateTime('end_at')->nullable(true)->default(null);

            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('client_id')->nullable(true);
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('banners');
    }
}

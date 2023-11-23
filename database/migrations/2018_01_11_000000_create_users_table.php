<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email');
            $table->string('cpf')->unique();
            $table->string('areaCode')->nullable()->default(null);
            $table->string('activationStatusCode')->default('NONVERIFIED');
            $table->string('password');
            $table->timestamp('lastSignin')->default(now());
            $table->integer('signinAttempts')->default(0);
            $table->rememberToken();

            $table->json('integrationCredentials')->nullable()->default(null);

            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());

            $table->softDeletes('deletedAt');

            $table->unsignedInteger('roleId');
            $table->foreign('roleId')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('roleId');
            $table->dropColumn('roleId');
        });
        Schema::drop('users');
    }
}

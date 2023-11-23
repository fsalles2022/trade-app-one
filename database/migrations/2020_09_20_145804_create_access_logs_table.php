<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessLogsTable extends Migration
{
    /** @return void */
    public function up(): void
    {
        if (!Schema::hasTable('access_logs')) {
            Schema::create('access_logs', static function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('userId');
                $table->ipAddress('ip');
                $table->string('device', 100)->nullable();
                $table->enum('type', ['signin', 'signout'])->default('signin');
                $table->string('requestedUrl')->nullable();
                $table->timestamps();

                $table->foreign('userId')->references('id')->on('users');
            });
        }
    }

    /** @return void */
    public function down(): void
    {
        if (Schema::hasTable('access_logs')) {
            Schema::dropIfExists('access_logs');
        }
    }
}

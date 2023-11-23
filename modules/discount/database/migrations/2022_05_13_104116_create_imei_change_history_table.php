<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImeiChangeHistoryTable extends Migration
{
    public function up(): void
    {
        Schema::create('imeiChangeHistory', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('serviceTransaction')->index();
            $table->timestamp('exchangeDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('oldImei')->nullable();
            $table->string('newImei')->nullable();
            $table->unsignedInteger('userIdWhoChanged');
            $table->string('userCpfWhoChanged')->index();
            $table->unsignedInteger('userIdWhoAuthorized');
            $table->string('userCpfWhoAuthorized')->index();
            $table->string('protocol')->nullable()->index();

            $table->timestamp('createdAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updatedAt')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes('deletedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imeiChangeHistory');
    }
}

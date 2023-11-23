<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Terms\Enums\StatusUserTermsEnum;

class CreateUserTermsTable extends Migration
{
    public function up(): void
    {
        Schema::create('userTerms', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('userId');
            $table->unsignedInteger('termId');
            $table->enum('status', StatusUserTermsEnum::AVAILABLE_STATUS)
                ->default(StatusUserTermsEnum::VIEWED);
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');

            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('termId')->references('id')->on('terms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userTerms');
    }
}

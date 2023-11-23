<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Terms\Enums\TypeTermsEnum;

class CreateTermsTable extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->string('urlEmbed');
            $table->boolean('active')->default(1);
            $table->enum('type', TypeTermsEnum::TERM_TYPE);
            $table->timestamp('createdAt')->default(now());
            $table->timestamp('updatedAt')->default(now());
            $table->softDeletes('deletedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
}

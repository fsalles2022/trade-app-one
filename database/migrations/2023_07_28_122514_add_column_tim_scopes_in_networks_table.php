<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTimScopesInNetworksTable extends Migration
{
    public function up(): void
    {
        Schema::table('networks', function (Blueprint $table): void {
            $table->json('timAuthentication')->nullable(true);
        });
    }

    public function down(): void
    {
        Schema::table('networks', function (Blueprint $table): void {
            $table->dropColumn('timAuthentication');
        });
    }
}

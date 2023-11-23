<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class InsertSurfPernambucanasPrePagoService extends Migration
{
    public function up(): void
    {
        Artisan::call(
            'db:seed',
            [
                '--class' => 'SurfPernambucanas\\Database\\Seed\\ServicesSeeder',
                '--force' => true,
            ]
        );
    }

    public function down(): void
    {
        //
    }
}

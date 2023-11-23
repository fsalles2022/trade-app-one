<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;

class InsertPernambucanasPreRecargaService extends Migration
{
    public function up(): void
    {
        Artisan::call(
            'db:seed',
            [
                '--class' => 'SurfPernambucanas\\Database\\Seed\\ServicesRechargeSeeder',
                '--force' => true,
            ]
        );
    }

    public function down(): void
    {
        //
    }
}

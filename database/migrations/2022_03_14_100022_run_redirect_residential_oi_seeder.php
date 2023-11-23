<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class RunRedirectResidentialOiSeeder extends Migration
{    
    public function up(): void
    {
        Artisan::call('db:seed',[
            '--class' =>RedirectResidentialOiSeeder::class,
            '--force' => true,
            ]);
    }
    
    public function down(): void
    {
        //
    }
}

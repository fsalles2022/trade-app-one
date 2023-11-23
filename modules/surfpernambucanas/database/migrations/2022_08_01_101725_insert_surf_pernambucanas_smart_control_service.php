<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migration;
use SurfPernambucanas\Database\Seed\ServicesSmartControlSeeder;

class InsertSurfPernambucanasSmartControlService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call(
            'db:seed',
            [
                '--class' => ServicesSmartControlSeeder::class,
                '--force' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

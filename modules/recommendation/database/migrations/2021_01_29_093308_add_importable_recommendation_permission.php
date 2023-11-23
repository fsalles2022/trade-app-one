<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Recommendation\database\seeders\AddImportableRecommendationPermissionSeeder;

class AddImportableRecommendationPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $seeder = new AddImportableRecommendationPermissionSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from permissions where slug = ?', ['IMPORTABLE.SALE_RECOMMENDATION']);
    }
}

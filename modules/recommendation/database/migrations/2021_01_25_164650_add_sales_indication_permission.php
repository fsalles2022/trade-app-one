<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Recommendation\database\seeders\AddSalesIndicationPermissionSeeder;

class AddSalesIndicationPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $seeder = new AddSalesIndicationPermissionSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from permissions where slug = ?', ['SALE.INDICATION']);
    }
}

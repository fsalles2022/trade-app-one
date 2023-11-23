<?php

use Buyback\database\seeds\EvaluationsBonusPermissionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPermissionEvaluationBonusToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $seed = new EvaluationsBonusPermissionSeeder();
        $seed->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from permissions where slug = ?', ['IMPORTABLE.EVALUATIONS_BONUS']);
    }
}

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertRemoteSaleActionInServiceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        (new ServiceOptionsTableSeeder())->run('REMOTE_SALE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::delete('delete from serviceOptions where action = ?', ['REMOTE_SALE']);
    }
}

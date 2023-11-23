<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetUsersPasswordToDefaultSeeder extends Seeder
{
    public function run(): void
    {
        $PASSWORD_HASH = bcrypt('Trade@2021');

        $UPDATE_PASSWORD_STATEMENT = "UPDATE users SET password='${PASSWORD_HASH}'";
        DB::unprepared($UPDATE_PASSWORD_STATEMENT);
    }
}
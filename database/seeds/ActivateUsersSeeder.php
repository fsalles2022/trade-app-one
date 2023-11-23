<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivateUsersSeeder extends Seeder
{
    public function run()
    {
        $USER_STATUS = \TradeAppOne\Domain\Enumerators\UserStatus::ACTIVE;

        $UPDATE_PASSWORD_STATEMENT = "UPDATE users SET activationStatusCode='${USER_STATUS}'";
        $REMOVE_DELETE_AT = "UPDATE users SET deletedAt=null";
        $SIGN_IN_ATTEMPT = "UPDATE users SET signinAttempts=0";

        DB::unprepared($UPDATE_PASSWORD_STATEMENT);
        DB::unprepared($REMOVE_DELETE_AT);
        DB::unprepared($SIGN_IN_ATTEMPT);
    }
}
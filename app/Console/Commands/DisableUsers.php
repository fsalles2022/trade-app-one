<?php

namespace TradeAppOne\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\User;

class DisableUsers extends Command
{
    protected $signature = 'users:disable';

    const DAYS_TO_INACTIVE    = 30;
    const DAYS_TO_SOFT_DELETE = 90;

    public function handle()
    {
        $this->deleteUsers();
        $this->inactiveUsers();
    }

    private function inactiveUsers()
    {
        $daysToInactiveToInactive = Carbon::now()->subDays(self::DAYS_TO_INACTIVE)->toDateTimeString();

        $query = User::whereIn('activationStatusCode', [UserStatus::ACTIVE, UserStatus::VERIFIED])
            ->whereDate('lastSignin', '<', $daysToInactiveToInactive);

        $query->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $user->activationStatusCode = UserStatus::INACTIVE;
                $user->save();
            }
        });
    }

    private function deleteUsers(): void
    {
        $daysToSofDeleteString = Carbon::now()->subDays(self::DAYS_TO_SOFT_DELETE)->toDateTimeString();

        $query = User::whereDate('lastSignin', '<', $daysToSofDeleteString);

        $query->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $user->delete();
                $user->save();
            }
        });
    }
}

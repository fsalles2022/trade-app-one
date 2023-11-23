<?php

namespace TradeAppOne\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    public function before()
    {
        try {
            $role = auth()->user()->role;

            if (filled($role) && $role->permissions == 'all') {
                return true;
            }
        } catch (ErrorException $exception) {
            return false;
        }
    }
}

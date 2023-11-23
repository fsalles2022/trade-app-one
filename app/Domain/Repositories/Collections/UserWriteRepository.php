<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class UserWriteRepository
{
    public static function createUser($data, Role $role): User
    {
        $user = new User();
        $user->fill($data);
        $user->role()->associate($role);

        $user->save();
        return $user;
    }

    public static function updateUser(User $user, array $data): User
    {
        $user->fill($data);
        $user->restore();
        $user->save();
        return $user;
    }
}

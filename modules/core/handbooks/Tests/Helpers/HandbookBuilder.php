<?php

namespace Core\HandBooks\Tests\Helpers;

use Core\HandBooks\Models\Handbook;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class HandbookBuilder
{
    protected $user;

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function build(): Handbook
    {
        $user     = $this->user ?? (new UserBuilder())->build();
        $handbook = factory(Handbook::class)->make();

        $handbook->user()->associate($user);
        $handbook->save();

        return $handbook;
    }
}

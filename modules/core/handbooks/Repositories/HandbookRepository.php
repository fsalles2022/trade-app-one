<?php

namespace Core\HandBooks\Repositories;

use Core\HandBooks\Exceptions\HandbookExceptions;
use Core\HandBooks\Models\Handbook;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Tables\User;

class HandbookRepository
{
    public static function create(array $attributes): Handbook
    {
        return Handbook::create($attributes);
    }

    public static function find(int $id): Handbook
    {
        $handbook = Handbook::find($id);

        if ($handbook === null) {
            throw HandbookExceptions::notFound();
        }

        return $handbook;
    }

    public static function filter(User $user, array $filters = []): Builder
    {
        return (new HandbookFilter())
            ->context($user)
            ->apply($filters)
            ->getQuery();
    }
}

<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use ClaroBR\Exceptions\UserAuthAlternateExceptions;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;

class UserAuthAlternatesRepository extends BaseRepository
{
    protected $model = UserAuthAlternates::class;

    public function createUserAuthAlternates(string $matriculation, User $user): UserAuthAlternates
    {
        if ($this->hasAlternateAuth($matriculation, $user)) {
            throw UserAuthAlternateExceptions::documentAlreadyExists();
        }

        $userAuthAlternates = UserAuthAlternates::create([
            'document' => $matriculation,
            'userId' => $user->id
        ]);

        return $userAuthAlternates;
    }

    private function hasAlternateAuth(string $matriculation, User $user): bool
    {
        $documents = $this->createModel()->query()->where('document', $matriculation)->get();

        foreach ($documents as $document) {
            if ($document->user->getNetwork()->id === $user->getNetwork()->id) {
                return true;
            }
        }

        return false;
    }

    public function updateUserAuthAlternates(string $matriculation, User $user): UserAuthAlternates
    {
        return UserAuthAlternates::updateOrCreate(
            ['userId' => $user->id],
            ['document' => $matriculation]
        );
    }
}

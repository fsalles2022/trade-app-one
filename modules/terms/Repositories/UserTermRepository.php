<?php

declare(strict_types=1);

namespace Terms\Repositories;

use Terms\Models\UserTerm;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class UserTermRepository extends BaseRepository
{
    protected $model = UserTerm::class;

    public function findByUserAndTerm(int $termId, int $userId): ?UserTerm
    {
        return $this->where('termId', '=', $termId)
        ->where('userId', '=', $userId)
        ->first();
    }
}

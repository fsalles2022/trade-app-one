<?php

declare(strict_types=1);

namespace Terms\Repositories;

use Terms\Models\Term;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class TermRepository extends BaseRepository
{
    protected $model = Term::class;

    public function findLastActiveTermByType(?string $type): ?Term
    {
        return $this->where('active', '=', 1)
        ->where('type', '=', $type)
        ->orderBy('id', 'desc')
        ->first();
    }
}

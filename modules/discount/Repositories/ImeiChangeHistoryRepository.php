<?php

declare(strict_types=1);

namespace Discount\Repositories;

use Discount\Models\ImeiChangeHistory;
use Discount\Repositories\DTOs\ImeiChangeHistoryCreateDto;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class ImeiChangeHistoryRepository extends BaseRepository
{
    protected $model = ImeiChangeHistory::class;

    public function save(ImeiChangeHistoryCreateDto $imeiChangeHistoryDto): ImeiChangeHistory
    {
        return $this->create($imeiChangeHistoryDto->toArray());
    }
}

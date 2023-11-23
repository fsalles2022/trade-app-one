<?php

namespace TradeAppOne\Domain\Repositories\Tables;

use TradeAppOne\Domain\Models\Tables\AccessLog;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class AccessLogRepository extends BaseRepository
{
    protected $model = AccessLog::class;
}

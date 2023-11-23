<?php

namespace TradeAppOne\Domain\Repositories\Tables;

use TradeAppOne\Domain\Models\Tables\UserPendingRegistration;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class UserThirdPartyRegistrationRepository extends BaseRepository
{
    protected $model = UserPendingRegistration::class;
}

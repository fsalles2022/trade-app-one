<?php

namespace TradeAppOne\Domain\Services\Cancel;

use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;

interface ServiceCancel
{
    public function cancel(User $user, Service $service): ?string;
}

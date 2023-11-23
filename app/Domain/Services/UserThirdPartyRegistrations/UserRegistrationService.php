<?php

namespace TradeAppOne\Domain\Services\UserThirdPartyRegistrations;

use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;

interface UserRegistrationService
{
    public function getOperator(): string;

    public function runOneInAPI(User $user, PointOfSale $current): array;

    public function isSyncedInAPI(User $user): bool;

    public function isRegisteredInAPI(User $user): bool;
}

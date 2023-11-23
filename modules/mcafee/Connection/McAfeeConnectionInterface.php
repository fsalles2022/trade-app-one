<?php

namespace McAfee\Connection;

use Illuminate\Support\Collection;
use McAfee\Adapters\Response\McAfeeCancelSubscriptionResponseAdapter;
use McAfee\Adapters\Response\McAfeeDisconnectDevicesResponseAdapter;
use McAfee\Adapters\Response\McAfeeNewSubscriptionResponseAdapter;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;

interface McAfeeConnectionInterface
{
    public function newSubscription(Service $service): McAfeeNewSubscriptionResponseAdapter;

    public function cancelSubscription(Service $service): McAfeeCancelSubscriptionResponseAdapter;

    public function disconnectDevices(Service $service): McAfeeDisconnectDevicesResponseAdapter;

    public function plans(User $user = null, string $operation = null): Collection;
}

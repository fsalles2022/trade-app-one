<?php

namespace McAfee\Connection;

use Illuminate\Support\Collection;
use McAfee\Adapters\Request\McAfeeCancelSubscriptionRequestAdapter;
use McAfee\Adapters\Request\McAfeeDisconnectDevicesRequestAdapter;
use McAfee\Adapters\Request\McAfeeNewSubscriptionRequestAdapter;
use McAfee\Adapters\Response\McAfeeCancelSubscriptionResponseAdapter;
use McAfee\Adapters\Response\McAfeeDisconnectDevicesResponseAdapter;
use McAfee\Adapters\Response\McAfeeNewSubscriptionResponseAdapter;
use McAfee\Enumerators\McAfeePlansNetworks;
use TradeAppOne\Domain\Components\Helpers\XMLHelper;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;

class McAfeeConnection implements McAfeeConnectionInterface
{
    protected $mcAfeeClient;
    const DEFAULT_METHOD = "ProcessRequestWS";

    public function __construct(McAfeeSoapClient $client)
    {
        $this->mcAfeeClient = $client;
    }

    public function newSubscription(Service $service): McAfeeNewSubscriptionResponseAdapter
    {
        $xmlParameters            = McAfeeNewSubscriptionRequestAdapter::adapt($service);
        $response                 = $this->mcAfeeClient->execute(self::DEFAULT_METHOD, $xmlParameters);
        $mcAfeeCreateSubscription = XMLHelper::convertToArray($response->ProcessRequestWSResult);
        return new McAfeeNewSubscriptionResponseAdapter($mcAfeeCreateSubscription);
    }

    public function cancelSubscription(Service $service): McAfeeCancelSubscriptionResponseAdapter
    {
        $xmlParameters            = McAfeeCancelSubscriptionRequestAdapter::adapt($service);
        $response                 = $this->mcAfeeClient->execute(self::DEFAULT_METHOD, $xmlParameters);
        $mcAfeeCancelSubscription = XMLHelper::convertToArray($response->ProcessRequestWSResult);
        return new McAfeeCancelSubscriptionResponseAdapter($mcAfeeCancelSubscription);
    }

    public function disconnectDevices(Service $service): McAfeeDisconnectDevicesResponseAdapter
    {
        $xmlParameters           = McAfeeDisconnectDevicesRequestAdapter::adapt($service);
        $response                = $this->mcAfeeClient->execute(self::DEFAULT_METHOD, $xmlParameters);
        $mcAfeeDisconnectDevices = XMLHelper::convertToArray($response->ProcessRequestWSResult);
        return new McAfeeDisconnectDevicesResponseAdapter($mcAfeeDisconnectDevices);
    }

    public function plans(User $user = null, string $operation = null): Collection
    {
        $products = McAfeePlansNetworks::filter();
        if ($operation) {
            $products = $products->where('operation', $operation);
        }

        return $products->values();
    }
}

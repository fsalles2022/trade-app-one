<?php

namespace McAfee\Adapters\Request;

use McAfee\Connection\McAfeeHeaders;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeDisconnectDevicesRequestAdapter extends McAfeeBaseRequest implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null): string
    {
        $partnerId       = McAfeeHeaders::getPartnerId();
        $customerContext = self::getCustomerIdByService($service);
        $partnerRef      = $service->serviceTransaction;
        $productSku      = $service->product;
        $mcAfeeReference = $service->mcAfeeReference;
        $productQuantity = $service->license['quantity'];
        return "<PARTNERCONTEXT>
                  <HEADER>
                    <PARTNER PARTNER_ID=\"$partnerId\" />
                  </HEADER>
                  <DATA>
                    <CUSTOMERCONTEXT ID=\"$customerContext\" REQUESTTYPE=\"UPDATE\">
                      <ACCOUNT />
                      <ORDER PARTNERREF=\"$partnerRef-0\" REF=\"$mcAfeeReference\">
                        <ITEMS>
                          <ITEM SKU=\"$productSku\" QTY=\"$productQuantity\" ACTION=\"DC\" />
                        </ITEMS>
                      </ORDER>
                    </CUSTOMERCONTEXT>
                  </DATA>
                </PARTNERCONTEXT>";
    }
}

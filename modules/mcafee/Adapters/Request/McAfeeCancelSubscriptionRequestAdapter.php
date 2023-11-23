<?php

namespace McAfee\Adapters\Request;

use McAfee\Connection\McAfeeHeaders;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeCancelSubscriptionRequestAdapter extends McAfeeBaseRequest implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null): string
    {
        $partnerId                = McAfeeHeaders::getPartnerId();
        $customerContext          = self::getCustomerIdByService($service);
        $partnerRef               = $service->serviceTransaction;
        $productSku               = $service->product;
        $mainPhone                = $service->customer['mainPhone'];
        $mainPhoneWithCountryCode = MsisdnHelper::removeSumSign(CountryAbbreviation::BR, $mainPhone);
        $mcAfeeReference          = data_get($service, 'license.mcAfeeReference');
        $productQuantity          = data_get($service, 'license.quantity');
        $productKey               = data_get($service, 'license.mcAfeeProductKey');
        return "<PARTNERCONTEXT>
                  <HEADER>
                    <PARTNER PARTNER_ID=\"$partnerId\" />
                  </HEADER>
                  <DATA>
                    <CUSTOMERCONTEXT ID=\"$customerContext\" REQUESTTYPE=\"UPDATE\">
                      <ACCOUNT />
                      <ORDER PARTNERREF=\"$partnerRef\" REF=\"$mcAfeeReference\">
                        <ITEMS>
                          <ITEM SKU=\"$productSku\" QTY=\"$productQuantity\" ACTION=\"CN\">
                            <PRODUCTKEY>$productKey</PRODUCTKEY>
                            <PHONE NUMBER=\"$mainPhoneWithCountryCode\" />
                          </ITEM>
                        </ITEMS>
                      </ORDER>
                    </CUSTOMERCONTEXT>
                  </DATA>
                </PARTNERCONTEXT>";
    }
}

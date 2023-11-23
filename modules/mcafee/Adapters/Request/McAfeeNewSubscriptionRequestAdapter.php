<?php

namespace McAfee\Adapters\Request;

use McAfee\Connection\McAfeeHeaders;
use McAfee\Enumerators\McAfeeSKU;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeNewSubscriptionRequestAdapter extends McAfeeBaseRequest implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null): string
    {

        $partnerId         = McAfeeHeaders::getPartnerId();
        $customerContext   = self::getCustomerIdByService($service);
        $customerEmail     = $service->customer['email'];
        $customerFirstName = $service->customer['firstName'];
        $customerLastName  = $service->customer['lastName'];
        $customerPassword  = $service->customer['password'];
        $partnerRef        = $service->serviceTransaction;
        $productSku        = $service->product;
        $productQuantity   = $service->license['quantity'];

        $mainPhone = MsisdnHelper::removeSumSign(CountryAbbreviation::BR, $service->customer['mainPhone']);

        $phoneXml = null;

        if (McAfeeSKU::requireNumberPhone($productSku)) {
            $phoneXml = "<PHONE NUMBER=\"$mainPhone\" COUNTRYCODE=\"BR\"/>";
        }

        return "<PARTNERCONTEXT>
            <HEADER>
                <PARTNER PARTNER_ID=\"$partnerId\"/>
            </HEADER>
            <DATA>
                <CUSTOMERCONTEXT ID=\"$customerContext\" REQUESTTYPE=\"NEW\">
                    <ACCOUNT>
                        <EMAILADDRESS>$customerEmail</EMAILADDRESS>
                        <FIRSTNAME>$customerFirstName</FIRSTNAME>
                        <LASTNAME>$customerLastName</LASTNAME>
                        <PASSWORD>$customerPassword</PASSWORD>
                        <PREFERENCES>
                            <PREFERENCE TYPE=\"LANG\">pt-br</PREFERENCE>
                        </PREFERENCES>
                    </ACCOUNT>
                    <ORDER PARTNERREF=\"$partnerRef\" REF=\"11\">
                        <ITEMS>
                            <ITEM SKU=\"$productSku\" QTY=\"$productQuantity\" LIC_QTY=\"$productQuantity\" ACTION=\"PD\">
                            $phoneXml
                            </ITEM>
                        </ITEMS>
                    </ORDER>
                </CUSTOMERCONTEXT>
            </DATA>
            </PARTNERCONTEXT>";
    }
}

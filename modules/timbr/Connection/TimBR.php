<?php

namespace TimBR\Connection;

use TradeAppOne\Domain\Models\Tables\Network;

class TimBR
{
    protected const SCOPE_REDE              = 'TIMVarejoRede.rede';
    public const GRANT_TYPE                 = 'client_credentials';
    public const SCOPE_SALES_READ           = 'TIMVarejoRede.salesRead';
    public const SCOPE_SALES                = 'TIMVarejoRede.sales';
    public const SCOPE_CHANGE_PLAN          = 'TIMVarejoRede.changeplan';
    public const SCOPE_ELIGIBILITY_PLAN     = 'TIMVarejoRede.eligibilityplan';
    public const SCOPE_ELIGIBILITY_PRODUCTS = 'TIMVarejoRede.eligibilityProducts';
    public const SCOPE_LOYALTY_ELIGIBILITY  = 'TIMVarejoRede.loyaltyEligibility';
    public const SCOPE_CUSTOMER_GROUP       = 'TIMVarejoRede.customergroup';


    public static function getAuthScopes(): string
    {
        return TimBR::SCOPE_SALES . " "
            . TimBR::SCOPE_SALES_READ . " "
            . TimBR::SCOPE_CHANGE_PLAN . " "
            . TimBR::SCOPE_ELIGIBILITY_PLAN . " "
            . TimBR::SCOPE_ELIGIBILITY_PRODUCTS . " "
            . TimBR::SCOPE_LOYALTY_ELIGIBILITY;
    }

    public static function getAuthScopesByNetwork(Network $network): string
    {
        $timAuthentication = $network->getTimAuthentication();

        // Get custom scopes for Network
        if (isset($timAuthentication['scopes']) && ! empty($timAuthentication['scopes']) && is_string($timAuthentication['scopes'])) {
            return $timAuthentication['scopes'];
        }

        // Get default scopes
        return self::getAuthScopes();
    }

    public static function getWSO2Uri(): string
    {
        return config('integrations.timBR.wso2-uri');
    }

    public static function getOAGUri(): string
    {
        return config('integrations.timBR.oag-uri');
    }

    public static function getOAMUri(): string
    {
        return config('integrations.timBR.oam-uri');
    }

    public static function getEligibilityUri(): string
    {
        return config('integrations.timBR.tim-eligibility-uri');
    }

    public static function getPMIDUri(): string
    {
        return config('integrations.timBR.tim-pmid-uri');
    }

    public static function getOrderUri(): string
    {
        return config('integrations.timBR.tim-order-uri');
    }

    public static function getPremiumCommissioningUri(): string
    {
        return config('integrations.timBR.tim-premium-commissioning-uri');
    }

    public static function getExpressUri(): string
    {
        return config('integrations.timBR.express');
    }

    public static function getElDoradoToken(): string
    {
        return config('integrations.timBR.eldorado.apiToken');
    }

    public static function getElDoradoHeaders(): array
    {
        return ['x-api-key' => self::getElDoradoUri()];
    }

    public static function getElDoradoUri(): string
    {
        return config('integrations.timBR.eldorado.uri');
    }

    public static function getBrScanUri(): string
    {
        return config('integrations.timBR.brscan.uri');
    }

    public static function getBrScanGenerateAuthenticateApiUser(): string
    {
        return config('integrations.timBR.brscan.generate-authenticate-api-user');
    }

    public static function getBrScanGenerateAuthenticateApiPassword(): string
    {
        return config('integrations.timBR.brscan.generate-authenticate-api-password');
    }

    public static function getBrScanAuthenticateStatusApiUser(): string
    {
        return config('integrations.timBR.brscan.authenticate-status-api-user');
    }

    public static function getBrScanAuthenticateStatusApiPassword(): string
    {
        return config('integrations.timBR.brscan.authenticate-status-api-password');
    }

    public static function getBrScanGenerateSaleTermForSignatureApiUser(): string
    {
        return config('integrations.timBR.brscan.generate-sale-term-for-signature-api-user');
    }

    public static function getBrScanGenerateSaleTermForSignatureApiPassword(): string
    {
        return config('integrations.timBR.brscan.generate-sale-term-for-signature-api-password');
    }

    public static function getBrScanSaleTermStatusApiUser(): string
    {
        return config('integrations.timBR.brscan.sale-term-status-api-user');
    }

    public static function getBrScanSaleTermStatusApiPassword(): string
    {
        return config('integrations.timBR.brscan.sale-term-status-api-password');
    }

    public static function getBrScanWelcomeKitApiUser(): string
    {
        return config('integrations.timBR.brscan.welcome-kit-api-user');
    }

    public static function getBrScanWelcomeKitApiPassword(): string
    {
        return config('integrations.timBR.brscan.welcome-kit-api-password');
    }
}

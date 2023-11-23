<?php

namespace Outsourced\Partner\Helpers\Traits;

use Outsourced\Partner\Constants\PartnerConstants;

trait AuthenticateUrlTrait
{
    public function mountSignInUrl($key, $subdomain = null, $tokenSeller = null): string
    {
        if ($subdomain !== null) {
            if (! empty($tokenSeller)) {
                return sprintf("https://%s.%s/%s?tokenSeller=%s", $subdomain, PartnerConstants::FRONT_END_SIGNIN_URL, $key, $tokenSeller);
            }
            return sprintf("https://%s.%s/%s", $subdomain, PartnerConstants::FRONT_END_SIGNIN_URL, $key);
        }

        if (! empty($tokenSeller)) {
            return sprintf("https://%s/%s?tokenSeller=%s", PartnerConstants::FRONT_END_SIGNIN_URL, $key, $tokenSeller);
        }

        return sprintf("https://%s/%s", PartnerConstants::FRONT_END_SIGNIN_URL, $key);
    }
}

<?php

namespace TimBR\Connection\Authentication;

final class TimBRCurlAuthRoutes
{
    const DEAFAULT_HEADERS = "-H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'";

    public static function CRED_SUBMIT(): string
    {
        return config('integrations.timBR.oam-curl') . "oam/server/auth_cred_submit";
    }

    public static function AUTHORIZE(): string
    {
        return config('integrations.timBR.oam-curl') . "ms_oauth/oauth2/endpoints/oauthservice/authorize";
    }

    public static function TOKENS(): string
    {
        return config('integrations.timBR.oam-curl') . "ms_oauth/oauth2/endpoints/oauthservice/tokens";
    }
}

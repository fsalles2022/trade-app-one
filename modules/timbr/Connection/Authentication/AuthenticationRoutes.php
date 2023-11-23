<?php

namespace TimBR\Connection\Authentication;

final class AuthenticationRoutes
{
    const FIRST_STEP  = '/ms_oauth/oauth2/endpoints/oauthservice/tokens';
    const AUTHORIZE   = 'ms_oauth/oauth2/endpoints/oauthservice/authorize';
    const CRED_SUBMIT = 'oam/server/auth_cred_submit';
}

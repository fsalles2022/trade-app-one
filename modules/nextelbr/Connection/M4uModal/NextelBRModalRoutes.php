<?php

namespace NextelBR\Connection\M4uModal;

class NextelBRModalRoutes
{
    public static function uriModal($authCode)
    {
        return config('integrations.nextel.modal.modal-uri') .'?'. http_build_query(['authCode' => $authCode]);
    }
}

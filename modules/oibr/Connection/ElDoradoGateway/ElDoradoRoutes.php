<?php

namespace OiBR\Connection\ElDoradoGateway;

final class ElDoradoRoutes
{
    public static function queryCreditCard(string $msisdn): string
    {
        return "baseunica/rest/v2/ext/usuario/{$msisdn}.msisdn";
    }
}

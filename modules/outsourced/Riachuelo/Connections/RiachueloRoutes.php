<?php

namespace Outsourced\Riachuelo\Connections;

final class RiachueloRoutes
{
    public static function deviceByImei(string $imei)
    {
        return "/automation/pdv/v1/dispositivos-moveis/imei/${imei}/descricao";
    }
}

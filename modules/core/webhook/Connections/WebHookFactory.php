<?php

namespace Core\WebHook\Connections;

use TradeAppOne\Domain\Models\Collections\Service;

class WebHookFactory
{
    public const DESTINATIONS = [
        //WebHookInova::class
    ];

    public static function make(Service $service): ?WebHookConnection
    {
        foreach (self::DESTINATIONS as $destiny) {
            $connection = resolve($destiny);

            if ($connection->isForMe($service)) {
                return $connection;
            }
        }

        return null;
    }
}

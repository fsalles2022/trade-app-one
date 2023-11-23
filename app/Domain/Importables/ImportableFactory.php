<?php

namespace TradeAppOne\Domain\Importables;

use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Exceptions\BusinessExceptions\ImportableNotFoundException;

class ImportableFactory
{
    public static $IMPORTABLES = Importables::IMPORTABLES;

    public static function make($importable, array $parameters = []): ImportableInterface
    {
        if (! array_key_exists($importable, Importables::IMPORTABLES)) {
            throw new ImportableNotFoundException();
        }

        return app()->makeWith(self::$IMPORTABLES[$importable], $parameters);
    }
}

<?php

namespace Uol\Adapters\Request;

class GerarPassaporteRequestAdapter
{
    public static function adapt(int $passportType)
    {
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <passaporte>
                <codigo_tipo>$passportType</codigo_tipo>
                <cliente></cliente>
            </passaporte>"
        );
    }
}

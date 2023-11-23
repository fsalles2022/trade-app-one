<?php

namespace Uol\Adapters\Request;

class CancelarPassaporteRequestAdapter
{
    public static function adapt(int $passportSerie)
    {
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <passaporte>
                <serie>$passportSerie</serie>
            </passaporte>"
        );
    }
}

<?php

namespace Uol\Adapters\Request;

class GerarPassaporteVinculadoAoCursoRequestAdapter
{
    public static function adapt(int $passportType, int $courseCode)
    {
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <passaporte>
                <codigo_tipo>$passportType</codigo_tipo>
                <codigo_curso>$courseCode</codigo_curso>
                <cliente></cliente>
            </passaporte>"
        );
    }
}

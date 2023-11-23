<?php

namespace Uol\Adapters\Request;

class ConsultarCategoriasRequestAdapter
{
    public static function adapt(string $language)
    {
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <categoria>
                <idioma>$language</idioma>                    
            </categoria>"
        );
    }
}

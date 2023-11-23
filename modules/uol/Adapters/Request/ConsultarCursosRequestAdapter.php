<?php

namespace Uol\Adapters\Request;

class ConsultarCursosRequestAdapter
{
    public static function adapt(int $categoryId)
    {
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <curso>
                <id_categoria>$categoryId</id_categoria>                    
            </curso>"
        );
    }
}

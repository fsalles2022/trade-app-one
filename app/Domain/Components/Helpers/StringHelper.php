<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Components\Helpers;

class StringHelper
{

    public static function removeSpecialcharactersAndAccent(string $value): string
    {
        $to   = ['ä', 'ã', 'à', 'á', 'â', 'ê', 'ë', 'è', 'é', 'ï', 'ì', 'í', 'ö', 'õ', 'ò', 'ó', 'ô', 'ü', 'ù', 'ú', 'û', 'À', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ', 'ç', 'Ç', '(', ')', ',', ';', ':', '|', '!', '"', '#', '$', '%', '&', '/', '=', '?', '~', '^', '>', '<', 'ª', 'º', '*', "'", '¨', '@'];
        $from = ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'O', 'u', 'U', 'u', 'U', 'A', 'A', 'E', 'I', 'O', 'U', 'n', 'N', 'c', 'C', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];

        return str_replace($to, $from, $value);
    }
}

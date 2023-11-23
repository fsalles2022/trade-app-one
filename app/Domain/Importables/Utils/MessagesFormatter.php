<?php

namespace TradeAppOne\Domain\Importables\Utils;

trait MessagesFormatter
{
    private function format(array $errors): string
    {
        $errorsMessages = '';

        foreach ($errors as $error) {
            $errorsMessages .= $error . ';\n ';
        }

        return $errorsMessages;
    }
}

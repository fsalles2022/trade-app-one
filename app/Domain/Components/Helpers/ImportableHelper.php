<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Illuminate\Support\Facades\Validator;
use TradeAppOne\Domain\Importables\ImportableInterface;

class ImportableHelper
{
    public static function hasErrorInLine(array $line, array $rules, array $columns)
    {
        $lineWithBeautyNames = [];
        $ruleForNiceNames    = [];
        foreach ($columns as $key => $niceName) {
            $ruleForNiceNames    += [$niceName => $rules[$key] ?? ''];
            $lineWithBeautyNames += [$niceName => $line[$key] ?? ''];
        }

        $validator = Validator::make($lineWithBeautyNames, $ruleForNiceNames);

        $validationError = $validator->errors()->first();

        if (filled($validationError)) {
            throw new \InvalidArgumentException($validationError);
        }
    }

    public static function makeLine(ImportableInterface $importable, array $parameters = []): array
    {
        $header  = array_keys($importable->getColumns());
        $content = $importable->getExample(...$parameters);
        return array_combine($header, $content);
    }
}

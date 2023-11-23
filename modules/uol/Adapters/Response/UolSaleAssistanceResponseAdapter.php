<?php

namespace Uol\Adapters\Response;

use Uol\Models\UolPassport;

class UolSaleAssistanceResponseAdapter
{
    public static function adapt(UolPassport $passport)
    {
        return array_filter([
            'message' => trans('uol::messages.passport_generated'),
            'activationCode' => $passport->number
        ], 'strlen');
    }
}

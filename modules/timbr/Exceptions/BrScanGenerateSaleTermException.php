<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class BrScanGenerateSaleTermException extends ThirdPartyExceptions
{
    public function getShortMessage(): string
    {
        return 'BrScanGenerateSaleTermException';
    }

    public function getDescription(): string
    {
        return trans('timBR::exceptions.brScan.generate_sale_term_fail');
    }
}

<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class BrScanSaleTermStatusException extends ThirdPartyExceptions
{
    public function getShortMessage(): string
    {
        return 'BrScanSaleTermStatusException';
    }

    public function getDescription(): string
    {
        return trans('timBR::exceptions.brScan.sale_term_fail');
    }
}

<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class BrScanGenerateLinkException extends ThirdPartyExceptions
{
    public function getShortMessage(): string
    {
        return 'BrScanGenerateLinkException';
    }

    public function getDescription(): string
    {
        return trans('timBR::exceptions.brScan.generate_link_fail');
    }
}

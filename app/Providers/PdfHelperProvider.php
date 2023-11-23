<?php

namespace TradeAppOne\Providers;

use TradeAppOne\Domain\Components\Printer\PdfHelper;

class PdfHelperProvider
{
    public function register()
    {
        app()->bind(PdfHelper::class, function () {
            return new PdfHelper();
        });
    }
}

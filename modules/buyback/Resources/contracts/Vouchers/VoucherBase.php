<?php


namespace Buyback\Resources\contracts\Vouchers;

use TradeAppOne\Domain\Components\Printer\PdfHelper;

abstract class VoucherBase
{
    abstract public function toHtml(): string;

    public function toPdf(): string
    {
        $html = $this->toHtml();

        $options = [
            'paper' => 'A4',
            'orientation' => 'portrait',
        ];


        return $this->pdfHelper()->fromHtmlToContent($html, $options);
    }

    private function pdfHelper(): PdfHelper
    {
        return resolve(PdfHelper::class);
    }
}

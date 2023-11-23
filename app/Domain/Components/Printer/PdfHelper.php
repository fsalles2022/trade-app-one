<?php

namespace TradeAppOne\Domain\Components\Printer;

use Dompdf\Dompdf;

class PdfHelper
{
    public function fromHtmlToContent($html, array $options): string
    {
        $pdf = new Dompdf();

        $pdf->loadHTML($html);
        $pdf->setPaper(
            data_get($options, 'paper', 'A4'),
            data_get($options, 'orientation', 'landscape')
        );
        $pdf->render();

        return $pdf->output();
    }
}

<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Layout;

use DateTime;
use Reports\SubModules\Core\Models\Operators;
use Reports\SubModules\Riachuelo\Hourly\Models\HierarchySaleAccumulatorCollection;
use Reports\SubModules\Riachuelo\Hourly\Models\PointOfSaleSaleAccumulatorCollectionList;
use TradeAppOne\Domain\Components\Printer\PdfHelper;

class HourlyReportPdfLayout
{
    /** @var DateTime */
    protected $startDate;

    /** @var DateTime */
    protected $endDate;

    /** @var Operators */
    protected $operators;

    /** @var PointOfSaleSaleAccumulatorCollectionList */
    protected $pointOfSaleSaleAccumulatorCollectionList;

    /** @var HierarchySaleAccumulatorCollection */
    protected $hierarchySaleAccumulatorCollection;

    public function __construct(
        DateTime $startDate,
        DateTime $endDate,
        Operators $operators,
        PointOfSaleSaleAccumulatorCollectionList $pointOfSaleSaleAccumulatorCollectionList,
        HierarchySaleAccumulatorCollection $hierarchySaleAccumulatorCollection
    ) {
        $this->startDate                                = $startDate;
        $this->endDate                                  = $endDate;
        $this->operators                                = $operators;
        $this->pointOfSaleSaleAccumulatorCollectionList = $pointOfSaleSaleAccumulatorCollectionList;
        $this->hierarchySaleAccumulatorCollection       = $hierarchySaleAccumulatorCollection;
    }

    /** Filepath is path + fileName */
    public function generate(string $filePath): string
    {
        $options = [
            'paper' => 'A4',
            'orientation' => 'portrait',
        ];

        $pdf = new PdfHelper();

        $content = $pdf->fromHtmlToContent(
            $this->renderHtml(),
            $options
        );

        $this->writeFile($content, $filePath);

        return $filePath;
    }


    private function renderHtml(): string
    {
        return view()
            ->file(
                __DIR__ . '/View/hourly_report_pdf.blade.php',
                [
                    'startDate'                                => $this->startDate,
                    'endDate'                                  => $this->endDate,
                    'operators'                                => $this->operators,
                    'pointOfSaleSaleAccumulatorCollectionList' => $this->pointOfSaleSaleAccumulatorCollectionList,
                    'hierarchySaleAccumulatorCollection'       => $this->hierarchySaleAccumulatorCollection
                ]
            )
            ->render();
    }

    private function writeFile(string $pdfContent, string $filePath): void
    {
        file_put_contents($filePath, $pdfContent);
    }
}

<?php

declare(strict_types=1);

namespace Buyback\Resources\contracts\Waybill;

use Buyback\Enumerators\WaybillCarriers;
use Buyback\Services\Waybill;
use Dompdf\Dompdf;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\View\View;

class WaybillLayout
{
    private $waybill;

    /**
     * @var WaybillFormatter
     */
    private $htmlFormatter;

    /**
     * @var Application|mixed
     */
    private $pdfFormatter;

    public function __construct(Waybill $waybill)
    {
        $this->waybill       = $waybill;
        $this->htmlFormatter = new WaybillFormatter($this->waybill);

        $this->pdfFormatter = new Dompdf([
            'paper' => 'A4',
            'orientation' => 'portrait',
        ]);
    }

    public function toPdf(): string
    {
        $this->pdfFormatter->loadHTML($this->view()->render());
        $this->pdfFormatter->render();

        return $this->pdfFormatter->output();
    }

    /**
     * @return Factory|Application|View
     */
    public function view(): View
    {
        return view('pdfs.waybill', [
            'waybill' => $this->waybill,
            'htmlFormatter' => $this->htmlFormatter
        ]);
    }

    private function createInfo(): string
    {
        return $this->htmlFormatter->format();
    }

    private function createRowsOfDevices(): string
    {
        $devicesRow = '';

        foreach ($this->waybill->services as $service) {
            $devicesRow .= "
            <tr class=\"tr-list\" >
                <th> {$service->serviceTransaction} </th>
                <th> {$service['device']['imei']} </th>
                <th> {$service['device']['label']} </th>
                <th> {$service['waybill']['auditor']['firstName']} {$service['waybill']['auditor']['lastName']} </th>
                <th> {$this->brazilianDate()} </th>
                <th> </th>
            </th>";
        }

        return $devicesRow;
    }

    private function createAdvisor($title): string
    {
        $advisor = '<div class="advisor">';

        $advisor .= '<div class="advisor-line">';
        $advisor .= '__________________________________';
        $advisor .= '</div>';
        $advisor .= '<h2>' . $title . '</h2>';

        $advisor .= '</div >';

        return $advisor;
    }

    private function BrazilianDate(): string
    {
        return $this->waybill->date->format('d/m/Y');
    }

    public function getCarrier(): string
    {
        return data_get(
            WaybillCarriers::CARRIER_BY_OPERATION,
            $this->waybill->services->first()->operation,
            ''
        );
    }
}

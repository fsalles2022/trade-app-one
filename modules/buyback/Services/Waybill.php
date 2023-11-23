<?php

declare(strict_types=1);

namespace Buyback\Services;

use Buyback\Enumerators\WaybillCarriers;
use Buyback\Resources\contracts\Waybill\WaybillFormatter;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Utils\File\IFileConvertable;

class Waybill implements IFileConvertable
{
    public $pointOfSale;
    public $services;
    public $date;
    public $id;
    public $servicesWithdrawnQuantity = 0;

    /**
     * @var WaybillFormatter
     */
    private $htmlFormatter;

    /**
     * @var Dompdf
     */
    private $pdf;

    public function __construct(
        PointOfSale $pointOfSale,
        Collection $services,
        Carbon $date
    ) {
        $this->pointOfSale = $pointOfSale;
        $this->services    = $services;
        $this->date        = $date;

        $this->htmlFormatter = new WaybillFormatter($this);

        $this->pdf = new Dompdf([
            'paper' => 'A4',
            'orientation' => 'portrait',
        ]);

        $this->processServicesWithdrawnQuantity();
    }

    private function processServicesWithdrawnQuantity(): void
    {
        $this->servicesWithdrawnQuantity = $this->services->where('waybill.withdrawn', true)->count();
    }

    public function toContents(): string
    {
        $this->pdf->loadHTML($this->view()->render());
        $this->pdf->render();

        return $this->pdf->output();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function view(): View
    {
        return view('pdfs.waybill', [
            'waybill' => $this,
            'htmlFormatter' => $this->htmlFormatter
        ]);
    }

    public function brazilianDate(): string
    {
        return $this->date->format('d/m/Y');
    }

    public function getCarrier(): string
    {
        return data_get(
            WaybillCarriers::CARRIER_BY_OPERATION,
            $this->services->first()->operation,
            ''
        );
    }
}

<?php

namespace Buyback\Resources\contracts\Vouchers\Iplace;

use Buyback\Resources\contracts\Vouchers\VoucherBase;
use Jenssegers\Date\Date;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Models\Collections\Sale;

class VoucherIplaceLayout extends VoucherBase
{
    public $city;
    public $date;
    public $device;
    public $customer;

    public function __construct(array $serviceData, Sale $saleData)
    {
        $this->city     = ucwords(mb_strtolower($saleData->pointOfSale['city']));
        $this->date     = (new Date($saleData->updatedAt))->format('d \d\e F \d\e Y');
        $this->device   = $serviceData['device'];
        $this->customer = $serviceData['customer'];
        $this->formatFields();
    }

    protected function formatFields(): void
    {
        $this->customer['fullName'] = $this->customer['firstName'] . ' ' . $this->customer['lastName'];
        $this->customer['cpf']      = FormatHelper::mask($this->customer['cpf'], '###.###.###-##');
    }

    public function toHtml(): string
    {
        view()->addLocation(__DIR__);
        return view('voucher-iplace', ['sale' => $this])->render();
    }

    public function getPath(): string
    {
        return (__DIR__ . '/iplace-logo-voucher.png');
    }
}

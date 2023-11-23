<?php

namespace Buyback\Resources\contracts\Vouchers\Riachuelo;

use Buyback\Resources\contracts\Vouchers\VoucherBase;
use Jenssegers\Date\Date;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Models\Collections\Sale;

class VoucherRiachueloLayout extends VoucherBase
{
    public $transactionId;
    public $city;
    public $date;
    public $device;
    public $price;
    public $customer;
    public $salesman;

    public function __construct(array $serviceData, Sale $saleData)
    {
        $this->transactionId         = $serviceData['serviceTransaction'];
        $this->city                  = ucwords(mb_strtolower($saleData->pointOfSale['city']));
        $this->date                  = (new Date($saleData->updatedAt))->format('d \d\e F \d\e Y');
        $this->salesman              = data_get($saleData, 'user.firstName'). '' .data_get($saleData, 'user.lasttName');
        $this->device                = $serviceData['device'];
        $this->price                 = $serviceData['price'];
        $this->customer              = $serviceData['customer'];
        $this->customer['mainPhone'] = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $this->customer['mainPhone']);

        $this->formatFields();
    }

    protected function formatFields(): void
    {
        $this->customer['cpf']       = FormatHelper::mask($this->customer['cpf'], '###.###.###-##');
        $this->customer['mainPhone'] = FormatHelper::mask($this->customer['mainPhone'], '(##) #####-#####');
        $this->customer['fullName']  = $this->customer['firstName'] . " " . $this->customer['lastName'];
        $this->customer['zipCode']   = FormatHelper::mask($this->customer['zipCode'], '#####-###');
    }

    public function toHtml(): string
    {
        view()->addLocation(__DIR__);
        return view('voucher-riachuelo', ['sale' => $this])->render();
    }
}

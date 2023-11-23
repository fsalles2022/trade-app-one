<?php

namespace Buyback\Resources\contracts;

use Buyback\Resources\contracts\Vouchers\VoucherBase;
use Jenssegers\Date\Date;
use stdClass;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Collections\Sale;

class VoucherLayout extends VoucherBase
{
    public $transactionId;
    public $city;
    public $date;
    public $device;
    public $price;
    public $customer;
    public $salesman;
    public $thirdParty;

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
        $this->thirdParty            = $this->infoThirdParty($saleData, $serviceData);

        $this->formatFields();
    }

    protected function formatFields()
    {
        $this->customer['cpf']       = FormatHelper::mask($this->customer['cpf'], '###.###.###-##');
        $this->customer['mainPhone'] = FormatHelper::mask($this->customer['mainPhone'], '(##) #####-#####');
        $this->customer['fullName']  = $this->customer['firstName'] . " " . $this->customer['lastName'];
        $this->customer['zipCode']   = FormatHelper::mask($this->customer['zipCode'], '#####-###');
    }

    public function toHtml(): string
    {
        view()->addLocation(__DIR__);
        return view('voucher', ['sale' => $this])->render();
    }

    private function infoThirdParty($saleData, $serviceData): stdClass
    {
        $network = data_get($saleData, 'pointOfSale.network.slug');

        $thirdParty         = new stdClass();
        $thirdParty->exists = false;

        if ($network === NetworkEnum::CEA) {
            $thirdParty->exists = true;
            $thirdParty->title  = trans('buyback::messages.voucher_thirdParty_cea_title');
            $thirdParty->value  = data_get($serviceData, 'register.card');
        }

        return $thirdParty;
    }
}

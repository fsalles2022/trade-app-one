<?php

namespace Buyback\Resources\contracts\Vouchers\IplaceAndroid;

use Buyback\Resources\contracts\Vouchers\VoucherBase;
use Jenssegers\Date\Date;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class VoucherIplaceAndroidLayout extends VoucherBase
{
    public $transactionId;
    public $city;
    public $date;
    public $device;
    public $price;
    public $customer;
    public $salesman;
    public $service;
    public $evaluationsBonusString;

    public function __construct(array $serviceData, Sale $saleData)
    {
        $this->transactionId          = data_get($serviceData, 'serviceTransaction');
        $this->city                   = ucwords(mb_strtolower(data_get($saleData->pointOfSale, 'city', '')));
        $this->date                   = (new Date($saleData->updatedAt))->format('d \d\e F \d\e Y');
        $this->salesman               = data_get($saleData, 'user.firstName'). '' .data_get($saleData, 'user.lastName');
        $this->device                 = data_get($serviceData, 'device');
        $this->price                  = data_get($serviceData, 'price');
        $this->customer               = data_get($serviceData, 'customer');
        $this->customer['mainPhone']  = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, data_get($this->customer, 'mainPhone'));
        $this->service                = $this->getTradeInService($saleData);
        $this->evaluationsBonusString = $this->getTradeInBonus();

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
        return view('voucher-iplace-android', ['sale' => $this])->render();
    }
    private function getTradeInService($sale): ?Service
    {
        $services = $sale->services;
        return $services->filter(static function (Service $service) {
            return $service->operator === Operations::TRADE_IN_MOBILE &&
                $service->operation === Operations::IPLACE_ANDROID && $service->sector === Operations::TRADE_IN;
        })->first();
    }

    private function getTradeInBonus(): ?string
    {

        $string    = null;
        $bonusList = data_get($this->service, 'evaluationsBonus', []);
        if (count($bonusList) > 0) {
            foreach ($bonusList as $bonus) {
                $string .= 'R$ '. data_get($bonus, 'bonusValue') . ' - Subsidio '. data_get($bonus, 'sponsor') .'; ';
            }
        }
        return $string;
    }
}

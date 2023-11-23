<?php

namespace Buyback\Services;

use Buyback\Assistance\TradeInSaleAssistance;
use Buyback\Exceptions\GeneratorVoucherException;
use Buyback\Resources\contracts\Vouchers\Iplace\VoucherIplaceLayout;
use TradeAppOne\Domain\Models\Collections\Service;

class TradeInIplaceService
{
    private $tradeInSaleAssistance;

    public function __construct(TradeInSaleAssistance $tradeInSaleAssistance)
    {
        $this->tradeInSaleAssistance = $tradeInSaleAssistance;
    }

    public function produceVoucherIplace(Service $service)
    {
        try {
            $serviceArray = $service->toArray();
            $saleEntity   = $service->sale;
            return (new VoucherIplaceLayout($serviceArray, $saleEntity));
        } catch (\Exception $exception) {
            throw new GeneratorVoucherException();
        }
    }
}

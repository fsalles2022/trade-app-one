<?php

declare(strict_types=1);

namespace Buyback\Console;

use Buyback\Assistance\TradeInSaleAssistance;
use Buyback\Services\TradeInService;
use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;

class TradeInCancelServiceCommand extends Command
{
    /** @var string */
    protected $signature = 'voucher:cancel';

    /** @var string */
    protected $description = 'Cancel voucher when to exceed 24 hours';

    public function handle(TradeInService $tradeInService, TradeInSaleAssistance $tradeInSaleAssistance): void
    {
        $sales = $tradeInService->getVoucherExpires();

        foreach ($sales as $sale) {
            $tradeInSaleAssistance->setStatusVoucher(
                $sale->services->first(),
                $sale->services->first()->serviceTransaction,
                ServiceStatus::CANCELED
            );
        }
    }
}

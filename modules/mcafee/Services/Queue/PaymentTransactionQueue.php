<?php

declare(strict_types=1);

namespace McAfee\Services\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use McAfee\DTO\PaymentTransactionDTO;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;

class PaymentTransactionQueue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var PaymentTransactionDTO */
    private $paymentTransactionDto;

    /** @var string */
    private $serviceTransaction;

    private const TRANSACTION_PAYMENT_MCAFEE = 'TRANSACTION_PAYMENT_MCAFEE';

    /** @param string[]|int[] $attributes */
    public function __construct(array $attributes, string $serviceTransaction)
    {
        $this->paymentTransactionDto = new PaymentTransactionDTO($attributes);
        $this->serviceTransaction    = $serviceTransaction;
    }

    /**
     * @throws SaleNotFoundException
     */
    public function handle(SaleRepository $saleRepository): void
    {
        $service = $saleRepository->findInSale($this->serviceTransaction);

        if ($service instanceof Service === false) {
            Log::info(self::TRANSACTION_PAYMENT_MCAFEE, ['ServiceNotFound' => $this->serviceTransaction]);
            return;
        }

        $saleRepository->updatePaymentStatus($service, $this->paymentTransactionDto->toArray());
    }
}

<?php

declare(strict_types=1);

namespace McAfee\Tests\Feature\Queue;

use Gateway\Enumerators\StatusPaymentTransaction;
use McAfee\Models\McAfeeMultiAccess;
use McAfee\Services\Queue\PaymentTransactionQueue;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class PaymentTransactionQueueTest extends TestCase
{
    use McAfeeFactoriesHelper;

    /** @var string|null */
    public $serviceTransaction;

    public function setUp()
    {
        parent::setUp();

        $service = factory(McAfeeMultiAccess::class)->create([
            'service.payment.status' => ServiceStatus::APPROVED
        ]);

        $serviceTransaction = (new SaleBuilder())->withServices([$service])->build()
                ->services->pluck('serviceTransaction')->toArray();

        $this->serviceTransaction = $serviceTransaction[0];
    }

    /** @test */
    public function should_update_payment_status_in_service_success(): void
    {
        (new PaymentTransactionQueue(
            [
                'create_time' => '1616803200',
                'status'      => '8'
            ],
            $this->serviceTransaction
        ))->handle($this->getInstanceSaleRepository());

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $this->serviceTransaction,
            'services.payment.status' => StatusPaymentTransaction::STATUS_PAYMENT[8],
        ], 'mongodb');
    }

    /** @test */
    public function should_return_throw__sale_not_found_exception(): void
    {
        $this->expectException(SaleNotFoundException::class);

        (new PaymentTransactionQueue(
            [
                'create_time' => '1616803200',
                'status'      => '8'
            ],
            '202112011551586074-0'
        ))->handle($this->getInstanceSaleRepository());
    }

    public function getInstanceSaleRepository(): SaleRepository
    {
        return resolve(SaleRepository::class);
    }
}

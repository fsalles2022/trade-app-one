<?php

declare(strict_types=1);

namespace McAfee\Tests\Feature;

use Illuminate\Support\Facades\Queue;
use McAfee\Services\Queue\PaymentTransactionQueue;
use TradeAppOne\Tests\TestCase;

class McAfeeUpdatePaymentStatusTest extends TestCase
{
    public const ENDPOINT_PAYMENT_TRANSACTION = 'sales-update/mcafee/';
    public $serviceTransaction                = '209458359423-0';

    /** @test */
    public function should_dispatch_job_payment_transaction_queue_success(): void
    {
        Queue::fake();

        $this->json(
            'POST',
            self::ENDPOINT_PAYMENT_TRANSACTION . $this->serviceTransaction,
            ['create_time' => '1616803200', 'status' => '6']
        )
            ->assertStatus(200);
        Queue::assertPushed(PaymentTransactionQueue::class);
    }

    /** @test */
    public function should_not_dispatch_job_method_not_allowed(): void
    {
        Queue::fake();

        $this->get(self::ENDPOINT_PAYMENT_TRANSACTION . $this->serviceTransaction)->assertStatus(405);
        Queue::assertNotPushed(PaymentTransactionQueue::class);
    }
}

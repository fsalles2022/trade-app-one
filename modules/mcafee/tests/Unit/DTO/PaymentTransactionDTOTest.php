<?php

declare(strict_types=1);

namespace McAfee\Tests\Unit\DTO;

use Gateway\Enumerators\StatusPaymentTransaction;
use McAfee\DTO\PaymentTransactionDTO;
use TradeAppOne\Tests\TestCase;

class PaymentTransactionDTOTest extends TestCase
{
    /** @return array[] */
    public function getAttributes(): array
    {
        return [
            [
                [
                    'create_time' => (string) strtotime('2021-03-27T21:00:00-03:00'),
                    'status' => "6",
                ],
                [
                    'createdAt' => '2021-03-27T21:00:00-03:00',
                    'status' => StatusPaymentTransaction::STATUS_PAYMENT[6]
                ],
            ],
            [
                [
                    'create_time' => (string) strtotime('2021-03-26T21:00:00-03:00'),
                    'status' => "1",
                ],
                [
                    'createdAt' => '2021-03-26T21:00:00-03:00',
                    'status' => StatusPaymentTransaction::STATUS_PAYMENT[1]
                ],
            ],
            [
                [
                    'create_time' => (string) strtotime('2021-03-25T21:00:00-03:00'),
                    'status' => "2",
                ],
                [
                    'createdAt' => '2021-03-25T21:00:00-03:00',
                    'status' => StatusPaymentTransaction::STATUS_PAYMENT[2]
                ],
            ],
            [
                [
                    'create_time' => (string) strtotime('2021-03-24T21:00:00-03:00'),
                    'status' => "3",
                ],
                [
                    'createdAt' => '2021-03-24T21:00:00-03:00',
                    'status' => StatusPaymentTransaction::STATUS_PAYMENT[3]
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getAttributes
     */
    public function should_return_hydrated_array_with_formatted_data(array $attributes, array $expectedArray)
    {
        $paymentTransaction = (new PaymentTransactionDTO($attributes))->toArray();
        self::assertEquals($expectedArray, $paymentTransaction);
    }
}

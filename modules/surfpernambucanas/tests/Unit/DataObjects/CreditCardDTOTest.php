<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\DataObjects;

use SurfPernambucanas\DataObjects\CreditCardDTO;
use TradeAppOne\Tests\TestCase;

class CreditCardDTOTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_created_credit_card_success(): void
    {
        $number = '1234567890';
        $cvv    = '998';
        $year   = '08';
        $month  = '01';

        $creditCard = new CreditCardDTO(
            $number,
            $cvv,
            $year,
            $month
        );

        $this->assertEquals($number, $creditCard->getNumber());
        $this->assertEquals($cvv, $creditCard->getCvv());
        $this->assertEquals("{$month}/{$year}", $creditCard->getValidity());
    }
}

<?php

namespace TradeAppOne\Tests\Unit\Domain\Helpers;


use TradeAppOne\Domain\Components\Helpers\BankDataHelper;
use TradeAppOne\Tests\TestCase;

class BankDataHelperTest extends TestCase
{

    /** @test */
    public function should_return_verification_digit()
    {
        $account = '12345';
        $digit = BankDataHelper::getVerificationDigit($account);

        $this->assertEquals(5, $digit);
    }

    /** @test */
    public function should_return_removed_verification_digit()
    {
        $accountRequest = '12345';
        $account = BankDataHelper::removeVerifyingDigit($accountRequest);

        $this->assertEquals(1234, $account);
    }

    /** @test */
    public function should_return_compose_account_with_operation_digit()
    {
        $operationRequest = "002";
        $accountRequest   = '12345';

        $account = BankDataHelper::composeAccount($accountRequest, $operationRequest);

        $this->assertEquals(1234002, $account);
    }

    /** @test */
    public function should_return_compose_account_without_operation_digit()
    {
        $operationRequest = "";
        $accountRequest   = '12345';

        $account = BankDataHelper::composeAccount($accountRequest, $operationRequest);

        $this->assertEquals(1234, $account);
    }
}
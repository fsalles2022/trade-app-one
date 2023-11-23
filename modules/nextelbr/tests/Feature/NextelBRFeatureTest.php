<?php

namespace NextelBR\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class NextelBRFeatureTest extends TestCase
{
    use AuthHelper;

    const VALIDATION_BANK_DATA = 'nextelbr/validation-bank-data';

    /** @test */
    public function should_return_status_412_when_bank_data_invalid()
    {
        $bankData = [
            'bankId'      => "756",
            'agency'      => "12345",
            'account'     => "543210",
        ];

        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post(self::VALIDATION_BANK_DATA, $bankData);

        $response->assertStatus(Response::HTTP_PRECONDITION_FAILED);
    }

    /** @test */
    public function should_return_status_200_when_bank_data_is_valid()
    {
        $bankData = [
            'bankId'      => "10",
            'agency'      => "12345",
            'account'     => "543210",
        ];

        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post(self::VALIDATION_BANK_DATA, $bankData);

        $response->assertStatus(Response::HTTP_OK);
    }
}

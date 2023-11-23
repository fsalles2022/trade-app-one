<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Feature;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class CreateExternalSaleFeatureTest extends TestCase
{
    use AuthHelper;

    public const URI = '/clarobr/v3/create-external-sale';

    /** @test */
    public function external_sale_created_success(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', self::URI, Siv3TestBook::SUCCESS_EXTERNAL_SALE);

        $response->assertStatus(201);
        $this->assertTrue(data_get(json_decode($response->getContent()), 'success', ''));
    }

    /** @test */
    public function external_sale_created_failure(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', self::URI, Siv3TestBook::FAILURE_EXTERNAL_SALE);

        $this->assertEquals(
            data_get(trans('siv::messages.activation.save_sale_failed'), 'message', ''),
            data_get(json_decode($response->getContent()), 'message', '')
        );
    }
}

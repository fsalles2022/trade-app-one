<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Feature;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class CheckExternalSaleFeatureTest extends TestCase
{
    use AuthHelper;

    public const URI = '/clarobr/v3/check-external-sale';

    /** @test */
    public function success_check_external_sale(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', self::URI, Siv3TestBook::SUCCESS_EXTERNAL_SALE);


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertFalse(
            data_get(json_decode($response->getContent()), 'saleExists', '')
        );
    }

    /** @test */
    public function failure_check_external_sale(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . self::URI, Siv3TestBook::FAILURE_EXTERNAL_SALE);


        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $response->getStatusCode());
        $this->assertEquals(
            data_get(trans('siv::messages.activation.check_sale_failed'), 'message', ''),
            data_get(json_decode($response->getContent()), 'message', '')
        );
    }
}

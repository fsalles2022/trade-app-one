<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBrProductsFeatureTest extends TestCase
{
    const ENDPOINT = '/clarobr/products';
    use AuthHelper, SivBindingHelper;

    /** @test */
    public function should_response_with_200_when_called_with_filters()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . self::ENDPOINT, ['areaCode' => ClaroBRTestBook::SUCCESS_PLANS_DDD]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_response_with_correct_products_structure()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . self::ENDPOINT, ['areaCode' => ClaroBRTestBook::SUCCESS_PLANS_DDD]);

        $productStructure = [
            [
                "product",
                "label",
                "price",
                "operator",
                "operation",
                "areaCode",
                "invoiceTypes",
                "promotion"
            ]
        ];
        $response->assertJsonStructure($productStructure);
    }

    /** @test */
    public function should_response_with_422_when_request_validate_area_code()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . self::ENDPOINT, ['areaCode' => '10']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /** @test */
    public function should_response_with_422_when_request_validate_operation()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . self::ENDPOINT, ['operation' => 'INVALID_OPERATION']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_request_validate_mode()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . self::ENDPOINT, ['mode' => 10]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

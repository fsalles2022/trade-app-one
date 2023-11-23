<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroRebateFeatureTest extends TestCase
{
    const ENDPOINT = '/sales/siv/rebate';

    use AuthHelper, SivBindingHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bindSivResponse();
    }

    /** @test */
    public function should_response_with_200_when_called_with_only_network()
    {
        $network = (new NetworkBuilder())
            ->withSlug(NetworkEnum::IPLACE)
            ->build();

        $user = (new UserBuilder())->withNetwork($network)->build();

        $queryString = http_build_query([
            'network' => 'IPLACE'
        ]);

        $this->authAs($user)
            ->get(self::ENDPOINT . '?' . $queryString)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'rebate' => [
                        '*' => [
                            '_id',
                            'model',
                            'manufacturer',
                            'sanitized'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function should_response_with_422_and_undefined_return_message()
    {
        $queryString = http_build_query([
            'network' => 'undefined',
            'plan' => 'undefined',
            'from' => 'undefined',
            'model' => 'undefined',
            'areaCode' => 'undefined',
        ]);

        $this->authAs((new UserBuilder())->build())
            ->get(self::ENDPOINT . '?' . $queryString)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                "message" => trans("validation.without_undefined")
            ]);
    }

    /** @test */
    public function should_response_with_invalid_structure_return_message()
    {
        $queryString = http_build_query([
            'network' => 'IPLACE',
            'plan' => ClaroBRTestBook::INVALID_PLAN_FOR_REBATE,
            'from' => 'cliente-base',
            'model' => 'iphone_xs_256',
            'areaCode' => '11',
        ]);

        $this->authAs((new UserBuilder())->build())
            ->get(self::ENDPOINT . '?' . $queryString)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => trans('siv::exceptions.' . ClaroExceptions::REBATE_WITH_INVALID_STRUCTURE)
            ]);
    }

    /** @test */
    public function should_response_with_200_when_query_is_correct()
    {
        $queryString = http_build_query([
            'network' => 'IPLACE',
            'plan' => ClaroBRTestBook::VALID_PLAN_FOR_REBATE,
            'from' => 'cliente-base',
            'model' => 'iphone_8_pl_64gb',
            'areaCode' => '47',
        ]);

        $this->authAs((new UserBuilder())->build())
            ->get(self::ENDPOINT . '?' . $queryString)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'type',
                'data' => [
                    'rebate' => [
                        'valor_pre',
                        'valor_plano',
                        'plano',
                        'multa'
                    ]
                ]
            ]);
    }
}

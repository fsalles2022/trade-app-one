<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\SalesToThirdPartiesFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class SalesToThirdPartiesFeatureTest extends TestCase
{
    use ElasticSearchHelper, ArrayAssertTrait, AuthHelper;

    private $userHelper;

    protected function setUp()
    {
        parent::setUp();
        $fixture = SalesToThirdPartiesFixture::fixture();
        $this->mockElasticSearchConnection($fixture);
        $this->be((new UserBuilder())->build());

        $this->userHelper = (new UserBuilder())->build();
    }

    /** @test */
    public function should_return_response_with_status_200_when_request_is_valid()
    {
        $response = $this->authAs($this->userHelper)
            ->post('reports/third_parties/sales');
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_with_correct_structure_when_request_is_valid()
    {
        $response = $this->authAs($this->userHelper)
            ->post('reports/third_parties/sales');
        $response->assertJsonStructure([
            [
                'cnpj',
                'total',
                'users' => [
                    [
                        'cpf',
                        'total',
                        'operators' => [
                            [
                                'operator',
                                'total',
                                'pre',
                                'pos',
                                'controle'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}

<?php

namespace Reports\Tests\Unit\Services;

use Reports\Tests\Fixture\SalesToThirdPartiesFixture;
use Reports\Services\SalesToThirdPartiesService;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class SalesToThirdPartiesServiceTest extends TestCase
{
    use ElasticSearchHelper, ArrayAssertTrait;

    private $salesToThirdPartiesService;

    protected function setUp()
    {
        parent::setUp();
        $fixture = SalesToThirdPartiesFixture::fixture();
        $this->mockElasticSearchConnection($fixture);
        $this->be((new UserBuilder())->build());

        $this->salesToThirdPartiesService = resolve(SalesToThirdPartiesService::class);
    }

    /** @test */
    public function should_return_an_instance()
    {
        $this->assertInstanceOf(SalesToThirdPartiesService::class, $this->salesToThirdPartiesService);
    }

    /** @test */
    public function should_return_with_correct_structure()
    {
        $sales = $this->salesToThirdPartiesService->getSales();

        $this->assertArrayStructure($sales, [
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

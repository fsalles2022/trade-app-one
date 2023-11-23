<?php

namespace Reports\Tests\Adapters;

use Reports\Adapters\QueryResults\SalesToThirdPartiesAdapter;
use Reports\Tests\Fixture\SalesToThirdPartiesFixture;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class SalesToThirdPartiesAdapterTest extends TestCase
{
    use ArrayAssertTrait;

    /** @test */
    public function should_return_with_correct_structure()
    {
        $fixture = SalesToThirdPartiesFixture::fixture();
        $adapted = SalesToThirdPartiesAdapter::adapt(collect($fixture));
        $this->assertArrayStructure($adapted, [
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

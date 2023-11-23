<?php

namespace TimBR\Tests\Unit\Adapters;

use TimBR\Adapters\TimBRElegibilityRequestAdapterV3;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class TimBRElegibilityRequestAdapterV3Test extends TestCase
{
    /** @test */
    public function should_return_area_code_when_portability()
    {
        $request = [
            'operation'     => Operations::TIM_CONTROLE_FATURA,
            'pointOfSale'  => '12',
            'transactionToken' => 'aaaa',
            'state'        => 'SP',
            'portedNumber' => '11991910045',
            'customer'     => [
                'cpf'       => '123123',
                'firstName' => 'ashd',
                'lastName'  => 'ashd',
                'filiation' => 'ashd',
                'birthday' => 'ashd',
                'zipCode'   => 'ashd',
            ]
        ];
        $result  = TimBRElegibilityRequestAdapterV3::adapt($request);
        self::assertArrayHasKey('ddd', $result['newContract']);
    }

    /** @test */
    public function should_return_area_code_when_new()
    {
        $request = [
            'operation'     => Operations::TIM_CONTROLE_FATURA,
            'pointOfSale'  => '12',
            'transactionToken' => 'aaaa',
            'state'        => 'SP',
            'areaCode' => '11991910045',
            'customer'     => [
                'cpf'       => '123123',
                'firstName' => 'ashd',
                'lastName'  => 'ashd',
                'filiation' => 'ashd',
                'birthday' => 'ashd',
                'zipCode'   => 'ashd',
            ]
        ];
        $result  = TimBRElegibilityRequestAdapterV3::adapt($request);
        self::assertArrayHasKey('ddd', $result['newContract']);
    }

    /** @test */
    public function should_return_msisdn_contract_when_exists()
    {
        $request = [
            'operation'     => Operations::TIM_CONTROLE_FATURA,
            'pointOfSale'  => '12',
            'transactionToken' => 'aaaa',
            'state'        => 'SP',
            'msisdn' => '11991910045',
            'customer'     => [
                'cpf'       => '123123',
                'firstName' => 'ashd',
                'lastName'  => 'ashd',
                'filiation' => 'ashd',
                'birthday' => 'ashd',
                'zipCode'   => 'ashd',
            ]
        ];
        $result  = TimBRElegibilityRequestAdapterV3::adapt($request);
        self::assertArrayHasKey('msisdn', $result['contract']);
    }
}

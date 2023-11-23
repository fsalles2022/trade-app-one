<?php

namespace TimBR\Tests\Unit\Adapters;

use TimBR\Adapters\TimBRElegibilityRequestAdapter;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class TimBREligibilityRequestAdapterTest extends TestCase
{
    use TimFactoriesHelper;

    /** @test */
    public function should_return_pdv_key_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('pdv', $adapted);
    }

    /** @test */
    public function should_return_customer_key_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('customer', $adapted);
        self::assertEquals('CONTROLE', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_contract_key_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('contract', $adapted);
        self::assertEquals('CONTROLE', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_plan_key_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('plan', $adapted);
        self::assertEquals('CONTROLE', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_plan_segment_CONTRLOLE_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertEquals('CONTROLE', $adapted['plan']['segment']);
    }


    /** @test */
    public function should_return_plan_segment_EXPRESS_when_eligibility_is_sent()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'msisdn'      => '11982737287',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_pdv_key_EXPRESS_when_eligibility_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('pdv', $adapted);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }


    /** @test */
    public function should_return_pdv_key_filled_EXPRESS_when_eligibility_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('pdv', $adapted);
        self::assertNotEmpty($adapted['pdv']);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }


    /** @test */
    public function should_return_customer_key_EXPRESS_when_eligibility_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('customer', $adapted);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }


    /** @test */
    public function should_return_customer_filled_EXPRESS_when_eligibility_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertNotEmpty($adapted['customer']);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_plan_key_EXPRESS_when_eligibility_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayHasKey('plan', $adapted);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_contract_empty_segment_EXPRESS_when_msisdn_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_EXPRESS,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertEquals('EXPRESS', $adapted['plan']['segment']);
    }

    /** @test */
    public function should_return_empty_contract_key_when_msisnd_empty()
    {
        $eligibility = [
            'pointOfSale' => '1',
            'state'       => 'required',
            'operation'   => Operations::TIM_CONTROLE_FATURA,
            'customer'    =>
                [
                    'firstName' => 'required',
                    'lastName'  => 'required',
                    'cpf'       => 'required',
                    'birthday'  => 'required',
                    'filiation' => 'required',
                    'zipCode'   => 'required'
                ]
        ];

        $adapted = TimBRElegibilityRequestAdapter::adapt($eligibility);
        self::assertArrayNotHasKey('contract', $adapted);
    }
}

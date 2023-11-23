<?php

namespace TradeAppOne\Features\Customer\Adapter;

use TradeAppOne\Tests\TestCase;

class CustomerNotNestedTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $customerService = new CustomerNotNested([]);
        $className       = get_class($customerService);
        $this->assertEquals(CustomerNotNested::class, $className);
    }

    /** @test */
    public function should_adapt_return_an_array()
    {
        $payload         = $this->mockedPayload();
        $customerService = new CustomerNotNested($payload);
        $className       = $customerService->adapt();
        $this->assertInternalType('array', $className);
    }

    /** @test */
    public function should_adapt_return_an_customer()
    {
        $payload         = $this->mockedPayload();
        $customerService = new CustomerNotNested($payload);
        $customer        = $customerService->adapt();

        $this->assertEquals($payload['cpf'], $customer['cpf']);
    }

    private function mockedPayload()
    {
        return [
            'firstName' => 'Maria',
            'lastName' => 'Jesus',
            'email' => 'ig@hotmail.com',
            'gender' => 'F',
            'birthday' => '1992-02-11',
            'filiation' => 'Joana Jesus',
            'mainPhone' => '+5511956226555',
            'secondaryPhone' => '+5511956235586',
            'salaryRange' => 1,
            'profession' => 1,
            'maritalStatus' => 1,
            'zipCode' => '08051380',
            'localId' => 110,
            'local' => 'Rua João Tavares',
            'state' => 'SP',
            'city' => 'São Paulo',
            'neighborhood' => 'Limoeiro',
            'number' => '1254',
            'complement' => 'Apartamento 23, Bloco 23',
            'rg' => '001494951',
            'rgState' => 'SP',
            'rgDate' => '2003-03-28',
            'rgLocal' => 'SSP',
            "cpf" => "85670735374",
        ];
    }
}

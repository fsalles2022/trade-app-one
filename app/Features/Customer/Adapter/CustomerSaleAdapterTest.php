<?php

namespace TradeAppOne\Features\Customer\Adapter;

use TradeAppOne\Features\Customer\Adapter\CustomerSaleAdapter;
use TradeAppOne\Tests\TestCase;

class CustomerSaleAdapterTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $customerService = new CustomerSaleAdapter([]);
        $className       = get_class($customerService);
        $this->assertEquals(CustomerSaleAdapter::class, $className);
    }

    /** @test */
    public function should_adapt_return_an_array()
    {
        $payload         = $this->mockedSavePayload([]);
        $customerService = new CustomerSaleAdapter($payload);
        $className       = $customerService->adapt();
        $this->assertInternalType('array', $className);
    }

    /** @test */

    public function should_adapt_return_an_customer()
    {
        $customerData = "68399903540";

        $payload         = $this->mockedSavePayload($customerData);
        $customerService = new CustomerSaleAdapter($payload);
        $customer        = $customerService->adapt();

        $this->assertEquals($customerData, $customer['cpf']);
    }

    private function mockedSavePayload($cpf)
    {
        return [
                array (
                    'product' => 'CB_2.5GB_ILIM_LOCAL',
                    'dueDate' => 1,
                    'iccid' => '89550536110016451187',
                    'areaCode' => '11',
                    'invoiceType' => 'VIA_POSTAL',
                    'operation' => 'CONTROLE_BOLETO',
                    'mode' => 'ACTIVATION',
                    'operator' => 'CLARO',
                    'customer' =>
                        array (
                            'cpf' => $cpf,
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
                            'rgLocal' => 'sspsp',
                        ),
                    'sector' => 'TELECOMMUNICATION',
                    'status' => 'REJECTED',
                    'serviceTransaction' => '2018070407432907-0',
                    '_id' => '5b3ca4d00f20c1173303b7c8',
                    'operatorIdentifiers' =>
                        array (
                            'venda_id' => 1519739,
                            'servico_id' => 1475815,
                        ),
                )
        ];
    }
}

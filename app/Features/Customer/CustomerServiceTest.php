<?php

namespace TradeAppOne\Features\Customer;

use Faker\Generator as Faker;
use TradeAppOne\Tests\TestCase;
use Faker\Provider\pt_BR\Person;

class CustomerServiceTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $customerService = new CustomerService(new CustomerRepository());
        $className       = get_class($customerService);
        $this->assertEquals(CustomerService::class, $className);
    }

    /** @test */
    public function should_return_null_when_cpf_is_invalid()
    {
        $customerService = new CustomerService(new CustomerRepository());
        $result          = $customerService->fetchCustomer('123123');
        $this->assertNull($result);
    }

    /** @test */
    public function should_return_null_when_customer_not_exists()
    {
        $customerService = new CustomerService(new CustomerRepository());
        $result          = $customerService->fetchCustomer('123123');
        $this->assertNull($result);
    }

    /** @test */
    public function should_return_customer_instance_when_customer_exists()
    {
        $mock = $this->getMockBuilder(CustomerRepository::class)
                ->disableOriginalConstructor()
                ->setMethods([ 'get'])
                ->getMock();
        $mock->method('get')->withAnyParameters()->willReturn(new Customer());

        $customerService = new CustomerService(new CustomerRepository());
        $result          = $customerService->fetchCustomer('123123');
        $this->assertNull($result);
    }

    /** @test */
    public function should_retain_customer_persist_new_customer()
    {
        $customerService = new CustomerService(new CustomerRepository());
        $mockedCustomer  = $this->mockedCustomer(false);
        $customerService->retainCustomer($mockedCustomer);

        $this->assertDatabaseHas('customers', ['cpf' => $mockedCustomer['cpf']], 'mongodb');
    }

    /** @test */
    public function should_retain_customer_update_existent_customer()
    {
        $customerService = new CustomerService(new CustomerRepository());
        $mockedCustomer  = $this->mockedCustomer(false);
        $customerService->retainCustomer($mockedCustomer);
        $newName = 'Eren';
        $customerService->retainCustomer(['cpf' => $mockedCustomer['cpf'], 'firstName' => $newName]);

        $customer = $customerService->fetchCustomer($mockedCustomer['cpf']);

        $this->assertEquals($newName, $customer['firstName']);
    }

    //TODO: create factory
    private function mockedCustomer($formatted = true): array
    {
        $faker = new Faker();
        $faker->addProvider(new Person($faker));
        $cpf = $faker->unique()->cpf($formatted);

        return [
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
            'rgDate' => '2003-03-28',
            'rgState' => 'SP',
            'rgLocal' => 'SSP'
        ];
    }
}

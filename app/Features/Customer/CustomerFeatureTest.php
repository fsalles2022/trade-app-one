<?php

namespace TradeAppOne\Features\Customer;

use Faker\Generator as Faker;
use Faker\Provider\pt_BR\Person;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class CustomerFeatureTest extends TestCase
{
    use AuthHelper;
    private $endpoint = '/customer';

    /** @test */
    public function get_should_return_status_200__without_cpf_formatted()
    {
        $userHelper = (new UserBuilder())->build();
        $token      = $this->loginUser($userHelper);

        $faker = new Faker();
        $faker->addProvider(new Person($faker));
        $cpf = $faker->unique()->cpf(false);

        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', "{$this->endpoint}/{$cpf}");

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_status_422_when_cpf_is_invalid()
    {
        $userHelper = (new UserBuilder())->build();
        $token      = $this->loginUser($userHelper);

        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', "{$this->endpoint}/34234");

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_with_customer_info()
    {
        $userHelper = (new UserBuilder())->build();
        $token      = $this->loginUser($userHelper);

        Customer::create($this->mockedCustomer());

        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', "{$this->endpoint}/64155569590");

        $response->assertJsonFragment(['cpf' => '64155569590', 'firstName' => 'Paulo']);
    }

    private function mockedCustomer($formatted = true): array
    {
        $faker = new Faker();
        $faker->addProvider(new Person($faker));
        $faker->unique()->cpf($formatted);

        return [
            'cpf' => '64155569590',
            'firstName' => 'Paulo',
            'lastName' => 'Silveira',
        ];
    }
}

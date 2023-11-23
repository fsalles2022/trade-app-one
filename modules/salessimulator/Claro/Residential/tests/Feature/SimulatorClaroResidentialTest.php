<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Tests\Feature;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use SalesSimulator\Claro\Residential\Exceptions\SalesSimulatorResidentialException;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SimulatorClaroResidentialTest extends TestCase
{
    use AuthHelper;

    public const SIMULATOR_ROUTE = 'simulator/residential-city-plans';

    public function test_should_throw_exception_address_not_found(): void
    {
        $user = UserBuilder::make()->build();

        $this->authAs($user)->post(self::SIMULATOR_ROUTE, [
            'name' => 'Teste Silva',
            'cpf' => '72330665822',
            'zipCode' => Siv3TestBook::ZIP_CODE_NOT_FOUND_ADDRESS,
            'birthdate' => '01-02-1993'
        ])
            ->assertStatus(404)
            ->assertJson(
                json_decode(SalesSimulatorResidentialException::addressNotExists()->render()->getContent(), true)
            );
    }

    public function test_should_return_an_collection_of_all_plans(): void
    {
        $user = UserBuilder::make()->build();

        $this->authAs($user)->post(self::SIMULATOR_ROUTE, [
            'name' => 'Teste Silva',
            'cpf' => '72330665822',
            'zipCode' => Siv3TestBook::SUCCESS_POSTAL_CODE,
            'birthdate' => '01-02-1993'
        ])
            ->assertStatus(200);
    }

    public function test_should_return_an_collection_only_claro_box(): void
    {
        $user = UserBuilder::make()->build();

        $this->authAs($user)->post(self::SIMULATOR_ROUTE, [
            'name' => 'Teste Silva',
            'cpf' => '72330665822',
            'zipCode' => '16012-370',
            'birthdate' => '01-02-1993'
        ])
            ->assertStatus(200);
    }
}

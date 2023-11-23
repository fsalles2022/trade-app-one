<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroBrViabilityTest extends TestCase
{
    private const ENDPOINT = '/clarobr/v3/viability/';

    use AuthHelper;

    /** @return \array[][] */
    public function customerViability(): array
    {
        return [
            [
                [
                    'customer' => [
                        'firstName' => 'Name First',
                        'lastName' => 'Last',
                        'number' => 123,
                        'cpf' => Siv3TestBook::SUCCESS_CPF_CREDIT,
                        'complement' => 'complement',
                        'zipCode' => str_replace('-', '', Siv3TestBook::SUCCESS_POSTAL_CODE),
                        'birthday' => '1996-07-20',
                    ]
                ],
                'status' => true,
                'type' => 'residential'
            ],
            [
                [
                    'customer' => [
                        'firstName' => 'Name First',
                        'lastName' => 'Last',
                        'number' => 123,
                        'cpf' => '00000008796',
                        'complement' => 'complement',
                        'zipCode' => '00000-080'
                    ]
                ],
                'status' => false,
                'type' => null
            ]
        ];
    }

    /** @dataProvider customerViability */
    public function test_should_response_200_with_correct_viability_status($customer, $status, $type): void
    {
        $user    = (new UserBuilder())->build();
        $service = factory(Service::class)->create($customer);

        $sale = (new SaleBuilder())->withServices([$service])->build();

        $this->authAs($user)
            ->get('/' . self::ENDPOINT . $sale->services->first()->serviceTransaction)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'viability' => [
                    'type' => $type,
                    'status' => $status,
                    'proposalId' => null
                ]
            ]);
    }
}

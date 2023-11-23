<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response as HttpResponse;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroAutenticaFeatureTest extends TestCase
{
    use SivBindingHelper, AuthHelper;

    private const ANALISE_AUTENTICA = '/sales/siv/analise-authenticate';
    private const SAVE_AUTENTICA    = '/sales/siv/save-status-authenticate';

    /** @test */
    public function should_return_correct_structure_when_autentica_is_valid(): void
    {
        $this->bindSivResponse();
        $pointOfSale = (new PointOfSaleBuilder)->withState('with_identifiers')->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $response = $this->authAs($user)->post(self::ANALISE_AUTENTICA, [
            'cpf' => ClaroBRTestBook::SUCCESS_CUSTOMER
        ]);

        $data = $response->json();
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        $this->assertTrue(filled($data['message']));
    }

    /** @test */
    public function should_return_correct_structure_when_autentica_is_invalid(): void
    {
        $this->bindSivResponse();

        $pointOfSale = (new PointOfSaleBuilder)->withState('with_identifiers')->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $response = $this->authAs($user)->post(self::ANALISE_AUTENTICA, [
            'cpf' => ClaroBRTestBook::ERROR_AUTENTICA
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals($data['httpCode'], HttpResponse::HTTP_NOT_FOUND);
    }

    /** @test */
    public function should_return_status_code_200_when_save_autentica_status(): void
    {
        $this->bindSivResponse();

        $pointOfSale = (new PointOfSaleBuilder)->withState('with_identifiers')->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $sale        = $this->saleBuilder($pointOfSale);
        $service     = $sale->services()->first();
        $this->authAs($user)->post(self::SAVE_AUTENTICA, [
            'cpf' => ClaroBRTestBook::SUCCESS_CUSTOMER,
            'serviceTransaction' => data_get($service, 'serviceTransaction', ''),
        ])->assertStatus(HttpResponse::HTTP_OK);
    }

    /** @test */
    public function should_return_status_code_404_when_not_save_authenticate(): void
    {
        $this->bindSivResponse();

        $pointOfSale = (new PointOfSaleBuilder)->withState('with_identifiers')->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $sale        = $this->saleBuilder($pointOfSale);
        $service     = $sale->services()->first();

        $payload = [
            'cpf' => ClaroBRTestBook::FAILURE_CUSTOMER,
            'serviceTransaction' => data_get($service, 'serviceTransaction', ''),
        ];

        $this->authAs($user)
            ->post(self::SAVE_AUTENTICA, $payload)
            ->assertStatus(HttpResponse::HTTP_OK)
            ->assertJsonStructure(['httpCode'])
            ->assertJsonFragment(['httpCode' => HttpResponse::HTTP_NOT_FOUND]);
    }

    private function saleBuilder($pointOfSale): Sale
    {
        $service = factory(Service::class)->make();
        return (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();
    }
}

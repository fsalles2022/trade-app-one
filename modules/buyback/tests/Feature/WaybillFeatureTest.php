<?php

namespace Buyback\Tests\Feature;

use Buyback\Enumerators\WaybillPermissions;
use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class WaybillFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endpointGenerate   = '/waybill/generate';
    protected $endpointAvailables = '/waybill/availables';

    /** @test */
    public function post_should_response_with_422_when_not_exists_devices()
    {
        $permission = WaybillPermissions::getFullName(WaybillPermissions::CREATE);
        $userHelper = (new UserBuilder())->withPermission($permission)->build();

        $response = $this->authAs($userHelper)
            ->post($this->endpointGenerate, ['cnpj' => $userHelper->pointsOfSale->first()->cnpj]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_pdf_when_exists_devices()
    {
        $permission  = WaybillPermissions::getFullName(WaybillPermissions::CREATE);
        $user        = (new UserBuilder())->withPermission($permission)->build();
        $pointOfSale = $user->pointsOfSale->first();

        (new WaybillBuilder())
            ->withDrawn()
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        $response = $this->authAs($user)
            ->post($this->endpointGenerate, ['cnpj' => $pointOfSale->cnpj]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee('PDF');
    }

    /** @test */
    public function post_should_response_403_when_not_permission_create_waybill()
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $user->pointsOfSale->first();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        $response = $this->authAs($user)
            ->post($this->endpointGenerate, ['cnpj' => $pointOfSale->cnpj]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_response_with_list_of_waybills()
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();

        (new WaybillBuilder())
            ->withPointOfSale($pointOfSale)
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        $user = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $response = $this->authAs($user)->post($this->endpointAvailables);

        $response->assertJsonStructure([
            '*' => [
                'pointOfSale', 'services', 'date'
            ]
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }
}

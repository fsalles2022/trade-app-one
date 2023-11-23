<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Models\ClaroBandaLarga;
use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\ClaroBRResponseBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class ClaroBandaLargaFeatureTest
{
    use AuthHelper, SivFactoriesHelper, SivBindingHelper;
    private $endpoint = 'sales';

    /** @test */
    public function should_save_sale_with_claro_banda_larga()
    {
        $this->bindSivResponse();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $service = $this->sivFactories()
            ->of(ClaroBandaLarga::class)
            ->make()
            ->toArray();

        $payload = [
            'pointOfSale' => $pointOfSale->id,
            'services'    => [
                array_merge($service, $this->getProductForm())
            ]
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(["messages" => "Venda salva com sucesso"]);
    }

    /** @test */
    public function should_activate_sale_with_claro_banda_larga()
    {
        $this->bindSivResponse();
        $hierarchy          = (new HierarchyBuilder())->build();
        $pointOfSale        = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userFactory        = factory(User::class)->make();
        $userFactory['cpf'] = ClaroBRTestBook::SUCCESS_USER;
        $roleFactory        = factory(Role::class)->make();
        $roleFactory->network()->associate($pointOfSale->network()->first())->save();
        $userFactory->role()->associate($roleFactory)->save();
        $userFactory->pointsOfSale()->attach($pointOfSale);

        $service = $this->sivFactories()
            ->of(ClaroBandaLarga::class)
            ->make()
            ->toArray();

        $payload = [
            'pointOfSale' => $pointOfSale->id,
            'services'    => [
                array_merge($service, $this->getProductForm())
            ]
        ];

        $payload['services'][0]['customer']['cpf'] = ClaroBRTestBook::SUCESS_CUSTOMER_BANDA_LARGA;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userFactory))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpoint, $payload);

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userFactory))
            ->json('PUT', '/' . $this->endpoint, ['serviceTransaction' => $serviceTransaction]);

        $response->assertStatus(Response::HTTP_OK);
    }

    private function getProductForm(): array
    {
        $claroBandaLargaPlans = file_get_contents(ClaroBRResponseBook::CLARO_BANDA_LARGA_PLANS);
        $jsonDecode           = json_decode($claroBandaLargaPlans);
        $productId            = $jsonDecode->data->data[0]->id;
        $promotionId          = $jsonDecode->data->data[0]->plans_area_code[0]->promotions[0]->id;
        $areaCode             = $jsonDecode->data->data[0]->plans_area_code[0]->ddd;

        return [
            'product'   => (string) $productId,
            'areaCode'  => $areaCode,
            'promotion' => $promotionId,
        ];
    }
}

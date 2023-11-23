<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Models\ClaroPos;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\ClaroBRResponseBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroPosFeatureTest extends TestCase
{
    use AuthHelper, BindInstance, SivFactoriesHelper, SivBindingHelper;
    private $endpoint = 'sales';

    /** @test */
    public function should_save_sale_with_claro_pos_pago()
    {
        $this->bindSivResponse();
        $this->bindMountNewAttributesFromSiv();

        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $service = $this->sivFactories()
            ->of(ClaroPos::class)
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
    public function should_activate_with_claro_pos_pago()
    {
        $this->bindSivResponse();
        $this->bindMountNewAttributesFromSiv();

        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $service = $this->sivFactories()
            ->of(ClaroPos::class)
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

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
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

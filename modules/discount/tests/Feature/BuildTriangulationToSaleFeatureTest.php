<?php

namespace Discount\Tests\Feature;

use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BuildTriangulationToSaleFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function post_should_create_sale_with_triangulation()
    {
        $helper = $this->helper();

        $response = $this->withHeader('client', 'WEB')->authAs($helper->user)->post('sales', $helper->payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['data' => [
            'sale' => [
                'services' => [
                    '*' => [
                        'device' => ['id', 'sku', 'discount', 'priceWith', 'priceWithout'],
                        'discount' => ['id', 'title', 'discount']
                    ]]]]]);
    }

    private function helper()
    {
        $helper              = new \stdClass();
        $helper->pointOfSale = (new PointOfSaleBuilder())->withState('with_identifiers')->build();
        $helper->network     = $helper->pointOfSale->network;
        $helper->user        = (new UserBuilder())->withPointOfSale($helper->pointOfSale)->withNetwork($helper->network)->build();
        $helper->pointOfSale = $helper->user->pointsOfSale->first();

        $helper->discount = (new DiscountBuilder())
            ->available($helper->network)
            ->withUser($helper->user)
            ->build();

        $helper->payload =  array (
            'pointOfSale' => $helper->pointOfSale->id,
            'services' =>
                array (
                    0 =>
                        array (
                            'operator' => 'OI',
                            'operation' => 'OI_CONTROLE_BOLETO',
                            'product' => 'OCSF115',
                            'mode' => 'MIGRATION',
                            'msisdn' => '11996479214',
                            'imei' => '337923003881155',
                            'device' =>
                                array (
                                    'id' => $helper->discount->devices->first()->id,
                                    'label' => 'MOTOROLA G TV 2 XT1069-PRETO',
                                ),
                            'discount' =>
                                array (
                                    'id' => $helper->discount->id,
                                    'discount' => 3244.44,
                                    'product' => null,
                                    'price' => 9274.38,
                                ),
                            'customer' =>
                                array (
                                    'cpf' => '00000009652',
                                    'firstName' => 'Lucas',
                                    'lastName' => 'Lima',
                                    'mainPhone' => '+5567234234234',
                                    'birthday' => '1996-12-17',
                                    'email' => 'mail@mail.com',
                                    'zipCode' => '06140000',
                                    'local' => 'M',
                                    'state' => 'SP',
                                    'city' => 'Barueri',
                                    'neighborhood' => 'AlphaTradeUp',
                                    'number' => '123',
                                    'complement' => '3432',
                                ),
                        ),
                ),
        );

        return $helper;
    }
}

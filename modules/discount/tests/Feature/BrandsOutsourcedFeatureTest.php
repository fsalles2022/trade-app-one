<?php


namespace Discount\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BrandsOutsourcedFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_response_with_status_200_when_call_brands_outsourced()
    {
        $response = $this->authAs((new UserBuilder())->build())
            ->get('brands-outsourced');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_response_formated_as_expected()
    {
        $user = (new UserBuilder())->build();
        factory(DeviceOutSourced::class)->create(['networkId' => $user->getNetwork()->id]);

        $response = $this->authAs($user)->json('GET', 'brands-outsourced');

        $response->assertJsonStructure([
            'brands' => [
                '*' => [
                    'id',
                    'label'
                ]
            ],
            'models'=> [
                '*'=>[
                    'id',
                    'model',
                    'label',
                    'brand',
                    'color',
                    'storage',
                    'networkId',
                ]
            ]
        ]);
    }

    /** @test */
    public function should_return_response_filtered_by_user_network()
    {
        $user = (new UserBuilder())->build();
        factory(DeviceOutSourced::class)->create(['networkId' => $user->getNetwork()->id]);
        factory(DeviceOutSourced::class)->create(['networkId' => 2]);

        $response = $this->authAs($user)
            ->get('brands-outsourced');

        self::assertCount(1, $response->original['brands']);
        self::assertCount(1, $response->original['models']);
    }
}

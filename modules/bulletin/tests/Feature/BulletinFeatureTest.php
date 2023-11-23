<?php
declare(strict_types=1);

namespace Bulletin\tests\Feature;

use Bulletin\Tests\Helpers\Builders\BulletinBuilder;
use Bulletin\tests\Helpers\Functions;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BulletinFeatureTest extends TestCase
{
    use AuthHelper;

    protected function setUp()
    {
        parent::setUp();
    }

    public function test_should_return_200_with_bulletins_available(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $bulletin    = (new BulletinBuilder())->withAttributes([
            'finalDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s')
        ])->withNetwork($network)->build();

        $bulletin->pointOfSale()->sync([$pointOfSale->id]);
        $bulletin->role()->sync([$user->role->id]);
        $bulletin->user()->sync([$user->id]);


        $this->authAs($user)->get('bulletin')
            ->assertStatus(Response::HTTP_OK)->assertJson([
                'current_page' => 1,
                'data' => [['id' => $bulletin->id, 'networkId' => $network->id, 'title' => $bulletin->title]]
            ]);
    }

    public function test_should_return_200_active_bulletin(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();

        $bulletin = (new BulletinBuilder())->withAttributes([
            'status' => 0,
            'finalDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s')
        ])->withNetwork($network)->build();

        $bulletin->pointOfSale()->sync([$pointOfSale->id]);
        $bulletin->role()->sync([$user->role->id]);
        $bulletin->user()->sync([$user->id]);

        $this->authAs($user)->put('bulletin/activate/' . $bulletin->id, [
            'status' => 1
        ])->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['message' => 'Status atualizada com sucesso.']);

        $this->assertDatabaseHas('bulletins', [
            'id' => $bulletin->id,
            'status' => 1
        ]);
    }

    public function test_should_return_200_confirm_bulletin(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();

        $bulletin = (new BulletinBuilder())->withAttributes([
            'finalDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s')
        ])->withNetwork($network)->build();

        $bulletin->pointOfSale()->sync([$pointOfSale->id]);
        $bulletin->role()->sync([$user->role->id]);

        $this->authAs($user)->put('bulletin/confirm/' . $bulletin->id)->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['message' => 'Status atualizada com sucesso.']);

        $this->assertEquals('1', $bulletin->user->first()->bulletinsUsers->seen);
    }

    public function test_should_return_200_when_get_user_bulletin(): void
    {
        $network     = (new NetworkBuilder())->build();
        $role        = (new RoleBuilder())->withNetwork($network)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withRole($role)->withPointOfSale($pointOfSale)->build();
        $bulletin    = (new BulletinBuilder())->withAttributes([
            'finalDate' => Carbon::now()->addMonth()->format('Y-m-d H:i:s'),
            'status' => true
        ])->withNetwork($network)->build();

        $bulletin->pointOfSale()->attach([$pointOfSale->id]);
        $bulletin->role()->attach([$user->role->id]);

        $this->authAs($user)->get('bulletin/user')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id'    => $bulletin->id,
                'title' => $bulletin->title
            ]);
    }

    public function test_should_return_201_when_create_bulletin(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $payload     = Functions::payloadStoreBulletin('', '', '');
        $attributes  = Functions::makeTestImage();

        $payload['imageDesktop'] = $attributes['uploadedFile'];

        $this->authAs($user)->post('bulletin', $payload);

        unlink($attributes['path']);
    }
}

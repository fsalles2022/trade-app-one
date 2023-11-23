<?php

namespace Discount\Tests\Feature;

use Carbon\Carbon;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Discount\Enumerators\DiscountModes;
use Discount\Enumerators\DiscountStatus;
use Discount\Exceptions\DiscountExceptions;
use Discount\Models\DeviceDiscount;
use Discount\Models\DiscountProduct;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Http\Response;
use stdClass;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\TriangulationPermission;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TriangulationWriteFeatureTest extends TestCase
{
    use AuthHelper, SivBindingHelper;

    public const DISCOUNTS = 'discounts/';

    protected function setUp()
    {
        parent::setUp();
        $this->bindSivResponse();
    }

    /** @test */
    public function post_should_create_new_discount(): void
    {
        $helper = self::helper(self::PERMISSION_CREATE());

        $response = $this->authAs($helper->user)->post(self::DISCOUNTS, $helper->payload);

        $discountId = $response->json('id');

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('discounts', self::expectedDiscount());
        $this->assertDatabaseHas('discount_products', self::expectedProducts($discountId));
        $this->assertDatabaseHas('devices_discounts', self::expectedDevice($helper->device, $discountId));
    }

    /** @test */
    public function put_should_switch_status_discount(): void
    {
        $userHelper = (new UserBuilder())->withPermissions(self::PERMISSION_EDIT())->build();
        $discount   = (new DiscountBuilder())->withUser($userHelper)->withStatus(DiscountStatus::ACTIVE)->build();
        $response   = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put(
                self::DISCOUNTS. 'switch-status/' . $discount->id,
                [
                    'status' => DiscountStatus::INACTIVE,
                ]
            );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(
            ['id', 'title', 'status', 'filterMode', 'startAt', 'endAt']
        );
        $response->assertJson([
            'status' => DiscountStatus::INACTIVE
        ]);
    }

    /** @test */
    public function post_should_create_new_discount_with_filterMode_CHOSEN(): void
    {
        $helper = self::helper(self::PERMISSION_CREATE(), DiscountModes::CHOSEN);
        $cnpjs  = $helper->cnpjs->pluck('id');

        $response = $this->authAs($helper->user)->post(self::DISCOUNTS, $helper->payload);

        $response->assertStatus(Response::HTTP_CREATED);

        foreach ($cnpjs as $cnpj) {
            $this->assertDatabaseHas('pointsOfSale_discounts', ['pointOfSaleId' => $cnpj]);
        }
    }

    /** @test */
    public function put_should_response_with_status_200_when_discount_exists(): void
    {
        $userHelper = (new UserBuilder())->withPermissions(self::PERMISSION_EDIT())->build();
        $discount   = (new DiscountBuilder())->withUser($userHelper)->build();
        $response   = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put(
                self::DISCOUNTS . $discount->id,
                [
                    'startAt' => Carbon::now(),
                    'endAt' => Carbon::now()->addDays(10)
                ]
            );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(
            ['id', 'title', 'status', 'filterMode', 'startAt', 'endAt']
        );
    }

    /** @test */
    public function put_should_response_with_status_422_when_discount_not_exists(): void
    {
        $userHelper = (new UserBuilder())->withPermissions(self::PERMISSION_EDIT())->build();
        $response   = $this->withHeader('Authorization', $this->loginUser($userHelper))
            ->put(self::DISCOUNTS . '1', ['status' => DiscountStatus::ACTIVE]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function put_should_response_with_status_403_when_not_permission_under_discount(): void
    {
        $permission = self::PERMISSION_EDIT();
        $roleAdmin  = (new RoleBuilder())->build();
        $userAdmin  = (new UserBuilder())->withRole($roleAdmin)->withPermissions($permission)->build();

        $roleAux = (new RoleBuilder())->withParent($roleAdmin)->build();

        $discount = (new DiscountBuilder())->withUser($userAdmin)->build();
        $userAux  = (new UserBuilder())->withPermissions($permission)->withRole($roleAux)->build();
        $response = $this->withHeader('Authorization', $this->loginUser($userAux))
            ->put(self::DISCOUNTS . $discount->id, ['status' => DiscountStatus::ACTIVE]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function put_should_update_products_triangulation_with_filter_all(): void
    {
        $helper = self::helper(self::PERMISSION_EDIT());

        $product = factory(DiscountProduct::class)->make(
            [
                'operator' => Operations::CLARO,
                'operation' => Operations::CLARO_CONTROLE_FACIL
            ]
        );

        $discount  = (new DiscountBuilder())->withUser($helper->user)->withProduct($product)->build();
        $productId = $discount->products->first()->id;

        $edit = [
            'products' => [
                [
                    'operator' => Operations::VIVO,
                    'operations' => array(Operations::VIVO_CONTROLE_CARTAO),
                ]
            ]
        ];

        $response = $this->authAs($helper->user)->put("discounts/$discount->id", $edit);

        $assertNew = [
            'discountId' => $discount->id,
            'operator' => Operations::VIVO,
            'operation' => Operations::VIVO_CONTROLE_CARTAO,
            'filterMode' => DiscountModes::ALL
        ];

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('discount_products', $assertNew);
        $this->assertNull(DiscountProduct::where('id', $productId)->first());
    }

    /** @test */
    public function put_should_update_products_triangulation_with_filter_chosen(): void
    {
        $helper = self::helper(self::PERMISSION_EDIT());

        $product = factory(DiscountProduct::class)->make(
            [
                'operator' => Operations::CLARO,
                'operation' => Operations::CLARO_CONTROLE_FACIL
            ]
        );

        $discount  = (new DiscountBuilder())->withUser($helper->user)->withProduct($product)->build();
        $productId = $discount->products->first()->id;

        $edit = [
            'products' => [
                [
                    'operator' => Operations::OI,
                    'operations' => array(Operations::OI_CONTROLE_CARTAO),
                    'plans' => [
                        array(
                            'id' => 'OCSF125',
                            'label' => 'B - Oi Mais Controle Avançado Bs - R$59,99',
                            'operation' => 'OI_CONTROLE_CARTAO',
                            'operator' => 'OI'
                        )
                    ],
                ]
            ]
        ];

        $response = $this->authAs($helper->user)->put("discounts/$discount->id", $edit);

        $assertNew = [
            'discountId' => $discount->id,
            'operator' => Operations::OI,
            'operation' => Operations::OI_CONTROLE_CARTAO,
            'product' => 'OCSF125',
            'filterMode' => DiscountModes::CHOSEN
        ];

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('discount_products', $assertNew);
        $this->assertDatabaseHas('discount_products', ['id' => $productId, 'deletedAt' => now()->toDateTimeString()]);
    }

    /** @test */
    public function put_should_update_devices_triangulation(): void
    {
        $helper   = self::helper(self::PERMISSION_EDIT());
        $discount = (new DiscountBuilder())->withUser($helper->user)->build();
        $deviceId = $discount->devices()->first()->id;
        $edit     = [
            'devices' => [
                [
                    'ids' => $helper->device->pluck('id')->toArray(),
                    'discount' => '100',
                ]
            ]
        ];

        $response = $this->authAs($helper->user)->put("discounts/$discount->id", $edit);

        $assertNew = [
            'deviceId' => $helper->device->id,
            'discount' => '100',
            'discountId' => $discount->id,
        ];

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('devices_discounts', ['id' => $assertNew['deviceId']]);
        $this->assertNull(DeviceDiscount::where('id', $deviceId)->first());
    }

    /** @test */
    public function put_should_activate_and_disable_discount(): void
    {
        $user     = (new UserBuilder())->withPermissions(self::PERMISSION_EDIT())->build();
        $discount = (new DiscountBuilder())->withStatus(DiscountStatus::INACTIVE)->withUser($user)->build();

        $this->authAs($user)->put(self::DISCOUNTS . $discount->id, ['status' => DiscountStatus::ACTIVE]);
        $this->assertEquals(DiscountStatus::ACTIVE, $discount->fresh()->status);

        $this->authAs($user)->put(self::DISCOUNTS . $discount->id, ['status' => DiscountStatus::INACTIVE]);
        $this->assertEquals(DiscountStatus::INACTIVE, $discount->fresh()->status);
    }

    /** @test */
    public function delete_should_return_403_when_user_not_have_permission(): void
    {
        $user = (new UserBuilder())->build();

        $response = $this->authAs($user)->delete('triangulations/1');
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
    }

    /** @test */
    public function delete_should_return_403_when_user_not_have_permission_under_triangulation(): void
    {
        $permission = TriangulationPermission::getFullName(TriangulationPermission::DELETE);
        $roleAdmin  = (new RoleBuilder())->build();
        $userAdmin  = (new UserBuilder())->withRole($roleAdmin)->build();

        $roleAux = (new RoleBuilder())->withParent($roleAdmin)->build();
        $userAux = (new UserBuilder())->withPermission($permission)->withRole($roleAux)->build();

        $discount = (new DiscountBuilder())->withUser($userAdmin)->build();

        $response = $this->authAs($userAux)->delete("triangulations/$discount->id");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(
            ['message' => trans('discount::exceptions.' . DiscountExceptions::HAS_NOT_AUTHORIZATION)]
        );
    }

    /** @test */
    public function delete_should_return_status_200_when_triangulation_deleted(): void
    {
        $permission = TriangulationPermission::getFullName(TriangulationPermission::DELETE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $discount   = (new DiscountBuilder())->withUser($user)->build();
        $response   = $this->authAs($user)->delete("triangulations/$discount->id");

        $response->assertStatus(Response::HTTP_OK);
    }

    private static function helper(array $permissions, string $mode = DiscountModes::ALL): stdClass
    {
        $helper          = new stdClass();
        $helper->network = factory(Network::class)->create(['slug' => 'cea']);
        $helper->user    = (new UserBuilder())->withNetwork($helper->network)->withPermissions($permissions)->build();
        $helper->device  = factory(DeviceOutSourced::class)->create(['networkId' => $helper->network->id]);
        $helper->cnpjs   = collect();
        if ($mode == DiscountModes::CHOSEN) {
            $helper->cnpjs = collect(
                [
                    (new PointOfSaleBuilder())->withUser($helper->user)->build(),
                    (new PointOfSaleBuilder())->withUser($helper->user)->build(),
                    (new PointOfSaleBuilder())->withUser($helper->user)->build()
                ]
            );
        }

        $helper->payload = array(
            'title' => 'Desconto Teste',
            'startAt' => now()->format('Y-m-d'),
            'endAt' => now()->addDay(7)->format('Y-m-d'),
            'products' =>
                array(
                    0 =>
                        array(
                            'operator' => 'OI',
                            'operations' => array('OI_CONTROLE_CARTAO'),
                            'plans' => [
                                array(
                                    'id' => 'OCSF125',
                                    'label' => 'B - Oi Mais Controle Avançado Bs - R$59,99',
                                    'operation' => 'OI_CONTROLE_CARTAO',
                                    'operator' => 'OI'
                                )
                            ],
                            'promotions' => array(),
                        ),
                    1 =>
                        array(
                            'operator' => 'OI',
                            'operations' => array('OI_CONTROLE_CARTAO'),
                            'plans' => [
                                array(
                                    'id' => 'OCSF115',
                                    'label' => 'B - Oi Mais Controle Intermediário G2 R$44,99',
                                    'operation' => 'OI_CONTROLE_CARTAO',
                                    'operator' => 'OI'
                                )
                            ],
                            'promotions' => array(318),
                        ),
                ),
            'devices' =>
                array(
                    0 =>
                        array(
                            'ids' => $helper->device->pluck('id')->toArray(),
                            'discount' => 350,
                        ),
                ),
            'filterMode' => $mode,
            'pointsOfSale' => $helper->cnpjs->pluck('cnpj')->toArray(),
        );

        return $helper;
    }

    private static function PERMISSION_EDIT(): array
    {
        return [
            factory(Permission::class)->create(
                [
                    'slug' => TriangulationPermission::getFullName(TriangulationPermission::EDIT)
                ]
            )
        ];
    }

    private static function PERMISSION_CREATE(): array
    {
        return [
            factory(Permission::class)->create(
                [
                    'slug' => TriangulationPermission::getFullName(TriangulationPermission::CREATE)
                ]
            )
        ];
    }

    private static function expectedDiscount(string $mode = DiscountModes::ALL): array
    {
        return array(
            'title' => 'Desconto Teste',
            'status' => DiscountStatus::ACTIVE,
            'filterMode' => $mode,
            'startAt' => now()->format('Y-m-d'),
            'endAt' => now()->addDay(7)->endOfDay()->format('Y-m-d H:i:s')
        );
    }

    private static function expectedProducts(int $discountId): array
    {
        return array(
            'operator' => 'OI',
            'operation' => 'OI_CONTROLE_CARTAO',
            'product' => 'OCSF125',
            'filterMode' => 'CHOSEN',
            'discountId' => $discountId,
        );
    }

    private static function expectedDevice($device, $discountId): array
    {
        return array(
            'deviceId' => $device->id,
            'discountId' => $discountId,
            'discount' => 350,
        );
    }
}

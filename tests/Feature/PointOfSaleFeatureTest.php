<?php

namespace TradeAppOne\Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\PointOfSalePermission;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\PointOfSaleHelper;
use TradeAppOne\Tests\TestCase;

class PointOfSaleFeatureTest extends TestCase
{
    use PointOfSaleHelper, AuthHelper;
    protected $endpointPrefix = '/points_of_sale';

    /** @test */
    public function get_should_response_with_status_200(): void
    {
        $user = (new UserBuilder())->build();
        $this->sameNetwork(2);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', '/' . $this->endpointPrefix . '/');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_a_valid_list(): void
    {
        $user = (new UserBuilder())->build();
        $this->sameNetwork();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', '/' . $this->endpointPrefix . '/');
        $response->assertJsonStructure(['data' => [$this->getPointOfSaleStructure()]]);
    }

    public function getPointOfSaleStructure(): array
    {
        return ["slug",
            "label",
            "cnpj",
            "tradingName",
            "companyName",
            "telephone",
            "areaCode",
            "zipCode",
            "local",
            "neighborhood",
            "state",
            "number",
            "city",
            "complement",
            "latitude",
            "longitude",
            "availableServicesRelation"];
    }

    /** @test */
    public function get_should_return_list_of_3_points_of_sale_when_user_has_CONTEXT_ALL(): void
    {
        $network           = (new NetworkBuilder())->build();
        $userHasContextAll = (new UserBuilder())->withNetwork($network)->build();
        (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $this->sameNetwork(2);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextAll))
            ->json('GET', $this->endpointPrefix);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_return_list_of_3_points_of_sale_when_user_has_CONTEXT_HIERARCHY(): void
    {
        $permissionHierarchy = factory(Permission::class)->create([
            'client' => 'API',
            'slug'   => SalePermission::CONTEXT_HIERARCHY
        ]);

        $permissionAll = factory(Permission::class)->create([
            'client' => 'API',
            'slug'   =>  SalePermission::CONTEXT_ALL
        ]);
        $userHasContextAll   = (new UserBuilder())->withPermissions([$permissionHierarchy])->build();
        $hierarchyParent     = (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $network                 = (new NetworkBuilder())->build();
        $userHasContextHierarchy = (new UserBuilder())->withNetwork($network)->withPermissions([$permissionAll])->build();
        $hierarchyChildren       = (new HierarchyBuilder())->withParent($hierarchyParent)->withUser($userHasContextHierarchy)->build();

        (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();
        (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyChildren)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextHierarchy))
            ->json('GET', $this->endpointPrefix);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_return_list_of_1_point_of_sale_when_user_has_CONTEXT_NON_EXISTENT(): void
    {
        $userHelper = (new UserBuilder())->build();

        $this->sameNetwork(2);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', $this->endpointPrefix);
        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function get_should_response_with_status_403_when_cnpj_is_valid_and_user_hasnt_permission(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $this->sameNetwork(1)[0];
        $url         = $this->endpointPrefix . "/{$pointOfSale->cnpj}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_response_with_a_valid_object_when_cnpj_is_valid_and_hasnt_permission(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $this->sameNetwork(1)[0];
        $url         = "{$this->endpointPrefix}/{$pointOfSale->cnpj}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function get_should_response_with_status_404_when_slug_is_invalid_and_has_permission(): void
    {
        $user = (new UserBuilder())->build();
        $url  = "{$this->endpointPrefix}/adaddawadw";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function get_should_response_with_status_404_when_slug_is_invalid_and_hasnt_permission(): void
    {
        $user = (new UserBuilder())->build();
        $url  = $this->endpointPrefix . "/2";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function get_should_response_with_status_200_when_cnpj_is_valid(): void
    {
        $user            = (new UserBuilder())->build();
        $pointOfSaleList = $this->sameNetwork();
        $url             = $this->endpointPrefix . "?cnpj={$pointOfSaleList[0]->cnpj}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_a_valid_object_when_cnpj_is_valid(): void
    {
        $userHasContextAll = (new UserBuilder())->build();
        $pointOfSaleList   = $this->sameNetwork(10);
        (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $url = $this->endpointPrefix . "?cnpj={$pointOfSaleList[5]->cnpj}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHasContextAll))
            ->json('GET', $url);
        $response->assertJsonStructure(['data' => [$this->getPointOfSaleStructure()]]);
    }

    /** @test */
    public function get_should_response_with_persisted_object_when_cnpj_is_valid(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $this->sameNetwork(2)[1];
        $url         = $this->endpointPrefix . "?cnpj={$pointOfSale->cnpj}";

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $this->assertDatabaseHas('pointsOfSale', ['id' => $pointOfSale->id]);
    }

    /** @test */
    public function get_should_response_with_status_200_when_cnpj_not_exists(): void
    {
        $user = (new UserBuilder())->build();
        $this->sameNetwork();
        $cnpjNonExistent = factory(PointOfSale::class)->make()->cnpj;
        $url             = $this->endpointPrefix . "?cnpj={$cnpjNonExistent}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_empty_array_when_cnpj_not_exists(): void
    {
        $user = (new UserBuilder())->build();
        $this->sameNetwork();

        $cnpjNonExistent = factory(PointOfSale::class)->make()->cnpj;
        $url             = $this->endpointPrefix . "?cnpj={$cnpjNonExistent}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertJsonFragment(['total' => 0, 'data' => []]);
    }

    /** @test */
    public function get_should_response_with_status_422_when_cnpj_is_greater_than_14_digits(): void
    {
        $user    = (new UserBuilder())->build();
        $invalid = factory(PointOfSale::class)->states('cnpj_too_long')->make();
        $url     = $this->endpointPrefix . "?cnpj={$invalid->cnpj}";

        $response = $this
            ->withheader('authorization', $this->loginUser($user))
            ->json('get', $url);

        $response->assertstatus(response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_all_collection_when_cnpj_is_empty(): void
    {
        $user = (new UserBuilder())->build();
        (new HierarchyBuilder())->withUser($user)->build();
        $url = $this->endpointPrefix . "?cnpj=";
        $this->sameNetwork(2);

        $response = $this
            ->withheader('authorization', $this->loginUser($user))
            ->json('get', $url);

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_response_with_persisted_object_when_label_sent(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $this->sameNetwork()[0];
        $url         = $this->endpointPrefix . "?label={$pointOfSale->label}";

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $this->assertDatabaseHas('pointsOfSale', ['id' => $pointOfSale->id]);
    }

    /** @test */
    public function get_should_response_with_status_all_collection_when_empty_label(): void
    {
        $user = (new UserBuilder())->build();
        (new HierarchyBuilder())->withUser($user)->build();
        $url = $this->endpointPrefix . "?label=";
        $this->sameNetwork(2);

        $response = $this
            ->withheader('authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function get_should_response_with_status_422_when_state_is_greater_than_2_digits(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('state_too_long')->make();
        $url         = $this->endpointPrefix . "?state={$pointOfSale->state}";

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $url);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_label_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['label']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_label_is_grater_than_255_chars(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('label_too_long')->make();
        $pointOfSale = $pointOfSale->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_label_already_exists(): void
    {
        $user                 = (new UserBuilder())->build();
        $pointOfSaleExistent  = $this->sameNetwork()[0];
        $pointOfSale          = factory(PointOfSale::class)->make()->toArray();
        $pointOfSale['label'] = $pointOfSaleExistent['label'];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_cnpj_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['cnpj']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_cnpj_is_greater_than_14_digits(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('cnpj_too_long')->make();
        $pointOfSale = $pointOfSale->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_cnpj_already_exists(): void
    {
        $user                = (new UserBuilder())->build();
        $pointOfSaleExistent = $this->sameNetwork()[0];
        $pointOfSale         = factory(PointOfSale::class)->make()->toArray();
        $pointOfSale['cnpj'] = $pointOfSaleExistent['cnpj'];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_tradingName_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['tradingName']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_tradingName_is_greater_than_255_chars(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('tradingName_too_long')->make();
        $pointOfSale = $pointOfSale->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_companyName_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['companyName']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_companyName_is_greater_than_255_chars(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('companyName_too_long')->make();
        $pointOfSale = $pointOfSale->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_network_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['network']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_409_when_network_not_exists(): void
    {
        $user                     = (new UserBuilder())->build();
        $pointOfSale              = factory(PointOfSale::class)->make(['networkId' => 2]);
        $pointOfSale              = $pointOfSale->toArray();
        $pointOfSale['networkId'] = 9999;

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_telephone_is_missing(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->make()->toArray();
        unset($pointOfSale['telephone']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_422_when_telephone_is_greater_than_11_chars(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = factory(PointOfSale::class)->states('telephone_too_long')->make();
        $pointOfSale = $pointOfSale->toArray();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_should_response_with_status_200_when_all_parameters_are_valid(): void
    {
        $user                     = (new UserBuilder())->withPermission(PointOfSalePermission::getFullName(PermissionActions::CREATE))->build();
        $network                  = factory(Network::class)->create();
        $hierarchy                = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();
        $pointOfSale              = factory(PointOfSale::class)->make()->toArray();

        $pointOfSale['network']['slug']         = $network->slug;
        $pointOfSale['hierarchy']           = ['slug' => $hierarchy->slug];
        $pointOfSale['providerIdentifiers'] = ['CLARO' => 'MX47'];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $this->assertDatabaseHas('pointsOfSale', [
           'slug'                => $pointOfSale['slug'],
           'label'               => $pointOfSale['label'],
           'cnpj'                => $pointOfSale['cnpj'],
           'networkId'           => $network->id,
           'hierarchyId'         => $hierarchy->id,
           'providerIdentifiers' => json_encode($pointOfSale['providerIdentifiers'])
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_return_403_when_user_has_permission_to_create(): void
    {
        $user                     = (new UserBuilder())->build();
        $network                  = factory(Network::class)->create();
        $hierarchy                = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();

        $payload = factory(PointOfSale::class)->make()->toArray();
        $payload['network']['slug']         = $network->slug;
        $payload['hierarchy'] = $hierarchy->toArray();

        $response = $this->authAs()->post($this->endpointPrefix . '/', $payload);

        $response->assertJsonFragment(['shortMessage' => UserExceptions::UNAUTHORIZED] );
    }

    /** @test */
    public function post_should_response_with_persisted_point_of_sale_when_all_parameters_are_valid(): void
    {
        $user                       = (new UserBuilder())->withPermission(PointOfSalePermission::getFullName(PermissionActions::CREATE))->build();
        $network                    = factory(Network::class)->create();
        $hierarchy                  = (new HierarchyBuilder())->withUser($user)->build();
        $pointOfSale                = factory(PointOfSale::class)->make()->toArray();
        $pointOfSale['network']['slug']     = $network->slug;
        $pointOfSale['hierarchy'] = ['slug' => $hierarchy->slug];
        $pointOfSale['providerIdentifiers'] = [''];

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpointPrefix, $pointOfSale);

        $this->assertTrue(PointOfSale::query()->where('cnpj', '=', $pointOfSale['cnpj'])->get()->isNotEmpty());
    }

    //TODO: This function shouldn't be here because it's testing the Controller instead of the endpoint.

    /** @test */
    public function delete_should_soft_delete_the_record_when_slug_is_valid(): void
    {
        $user        = (new UserBuilder())->build();
        $pointOfSale = $this->sameNetwork()[0];
        $url         = "/point_of_sales/{$pointOfSale->slug}";

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('DELETE', $url);

        $this->assertNotNull(
            PointOfSale::withTrashed()->where('id', $pointOfSale->id)->get()
        );
    }

    /** @test */
    public function get_should_return_200_when_user_have_permissions_to_view_point_of_sales(): void
    {
        $user     = (new UserBuilder())->build();
        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get("{$this->endpointPrefix}");
        $response->assertStatus(200);
    }

    /** @test */
    public function get_should_return_200_and_points_of_sales_user_logged(): void
    {
        $user = (new UserBuilder())->build();
        (new HierarchyBuilder())->withUser($user)->build();
        $url = $this->endpointPrefix . '/user/logged';

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get($url);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['*' => ["id", "slug","label", "cnpj"]]);
    }

    /** @test */
    public function put_should_return_200_when_pointOfSale_edited(): void
    {
        $user              = (new UserBuilder())->withPermission('POINT_OF_SALE.EDIT')->build();
        $hierarchy         = (new HierarchyBuilder())->withUser($user)->build();
        $hierarchyToUpdate = (new HierarchyBuilder())->withUser($user)->build();
        $pointOfSale       = (new PointOfSaleBuilder())->withUser($user)->withHierarchy($hierarchy)->build();

        $data = [
            "zipCode"    => "00000000",
	        "areaCode"   => "99",
	        "state"      => "AC",
	        "hierarchy"  => ['slug' => $hierarchyToUpdate->slug],
            'providerIdentifiers' => [
                "OI"  => "1011023",
		        "TIM" => "SP10_MGOESI_VA0008_A006",
		        "CLARO" => "CM2A",
		        "NEXTEL" => [
                    "cod" => "11111",
    	            "ref" => "72883"
                ]
            ]
        ];

        $response = $this
            ->authAs($user)
            ->put('/points_of_sale/edit/' . $pointOfSale->cnpj, $data);

        $response->assertStatus(Response::HTTP_OK);
    }
}

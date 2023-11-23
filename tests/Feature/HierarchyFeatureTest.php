<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Permissions\HierarchyPermissions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class HierarchyFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function get_should_return_hierarchies_authorized(): void
    {
        $user      = (new UserBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->build();
        (new HierarchyBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get('hierarchies');

        $this->assertCount(1, $response->json());
        $response->assertJsonFragment(['id' => $hierarchy->id]);
    }

    /** @test */
    public function get_hierarchies_return_200_with_file(): void
    {
        $network   = (new NetworkBuilder())->build();
        $user      = (new UserBuilder())->withNetwork($network)->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();

        $header = ['Regional', 'Slug', 'Rede'];
        $lines  = [
            $hierarchy->label,
            $hierarchy->slug,
            $network->label
        ];

        $csvArray = [$header, $lines];
        $fileExport = CsvHelper::arrayToCsv($csvArray)->getContent();

        $response = $this->authAs($user)
            ->get('/hierarchies/export')
            ->assertStatus(Response::HTTP_OK);

        $this->assertContains($fileExport, $response->content());
    }

    /** @test */
    public function should_return_403_when_user_has_not_permission_to_create_(): void
    {
        $network   = (new NetworkBuilder())->build();
        $user      = (new UserBuilder())->withNetwork($network)->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->build();

        $response = $this->authAs($user)
            ->post('/hierarchies/', self::payload($network, $hierarchy))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
    }

    /** @test */
    public function should_return_201_when_create_hierarchy(): void
    {
        $permission = HierarchyPermissions::getFullName(HierarchyPermissions::CREATE);
        $network    = (new NetworkBuilder())->build();
        $hierarchy  = (new HierarchyBuilder())->withNetwork($network)->build();
        $user       = (new UserBuilder())->withHierarchy($hierarchy)->withNetwork($network)->withPermission($permission)->build();
        $parent     = (new HierarchyBuilder())->withParent($hierarchy)->withNetwork($network)->build();

        $response = $this->authAs($user)
            ->post('/hierarchies/', self::payload($network, $parent))
            ->assertStatus(Response::HTTP_CREATED);

        $response->assertJsonFragment(['message' => trans('messages.hierarchy_create')]);

        $this->assertDatabaseHas('hierarchies',[
            'id'        => '3',
            'slug'      => 'district-sp3',
            'label'     => 'District SP3',
            'sequence'  => null,
            'parent'    => $parent->id,
            'deletedAt' => null,
            'networkId' => $network->id
        ]);
    }

    /** @test */
    public function should_return_403_when_user_has_not_permission_about_hierarchy(): void
    {
        $permission = HierarchyPermissions::getFullName(HierarchyPermissions::CREATE);
        $network    = (new NetworkBuilder())->build();
        $hierarchy  = (new HierarchyBuilder())->build();
        $parent     = (new HierarchyBuilder())->withParent($hierarchy)->withNetwork($network)->build();
        $user       = (new UserBuilder())->withNetwork($network)->withPermission($permission)->build();

        $response = $this->authAs($user)
            ->post('/hierarchies/', self::payload($network, $parent))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY)]);
    }

    /** @test */
    public function should_return_403_when_user_has_not_permission_about_network(): void
    {
        $permission = HierarchyPermissions::getFullName(HierarchyPermissions::CREATE);
        $network    = (new NetworkBuilder())->build();
        $hierarchy  = (new HierarchyBuilder())->withNetwork($network)->build();
        $user       = (new UserBuilder())->withPermission($permission)->build();

        $response = $this->authAs($user)
            ->post('/hierarchies/', self::payload($network, $hierarchy))
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $response->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_NETWORK)]);
    }

    private static function payload($network, $parent): array
    {
        return [
            'label'       => 'District SP3',
	        'parent'      => $parent->slug,
	        'slug'        => 'district-sp3',
	        'networkSlug' => $network->slug
        ];
    }
}

<?php

namespace TradeAppOne\Tests\Feature\Network;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class NetworkFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endpointPrefix = '/networks';

    /** @test */
    public function get_should_return_paginated_list_when_request(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();

        factory(Network::class, 10)->create();

        $this->authAs($user)
            ->json('GET', $this->endpointPrefix)
            ->assertJsonFragment(['total' => 12]);
    }

    /** @test */
    public function get_should_return_paginated_list_when_label_is_blank(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();

        factory(Network::class, 10)->create();

        $this->authAs($user)
            ->json('GET', "{$this->endpointPrefix}?label=")
            ->assertJsonFragment(['total' => 12]);
    }

    /** @test */
    public function get_should_return_paginated_list_when_cnpj_is_blank(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();

        factory(Network::class, 10)->create();

        $this->authAs($user)
            ->json('GET', "{$this->endpointPrefix}?cnpj=")
            ->assertJsonFragment(['total' => 12]);
    }

    /** @test */
    public function get_should_return_paginated_list_when_search_by_cnpj(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();

        $networks = factory(Network::class, 10)->create();

        $this->authAs($user)
            ->json('GET', "{$this->endpointPrefix}?cnpj={$networks[2]->cnpj}")
            ->assertJsonFragment(['to' => 1]);
    }

    /** @test */
    public function get_should_return_paginated_list_when_search_by_label(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();

        $networks = factory(Network::class, 10)->create();

        $this->authAs($user)
            ->json('GET', "{$this->endpointPrefix}?label={$networks[2]->label}")
            ->assertJsonFragment(['to' => 1]);
    }

    /** @test */
    public function should_return_return_filtered_by_operator(): void
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $user      = (new UserBuilder())->withHierarchy($hierarchy)->build();


        $service = factory(Service::class, 1)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => 'OPERATOR_TEST',
            'operation' => 'OPERATION_TEST'
        ]);

        $networks = factory(Network::class, 5)->create();

        $networks[1]->services()->attach($service->first()->id);

        $this->authAs($user)
            ->json('GET', "{$this->endpointPrefix}?operator=OPERATOR_TEST")
            ->assertJsonFragment([
                'cnpj' => $networks[1]->cnpj,
                'to' => 1
            ]);
    }
}

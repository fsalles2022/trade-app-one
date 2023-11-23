<?php

namespace Reports\Tests\Unit\Goals;

use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GoalsFeatureTest extends TestCase
{
    use AuthHelper;
    private $endpointPrefix = 'goals';

    /** @test */
    public function should_goals_with_get_return_200()
    {
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        (new HierarchyBuilder())->withUser($user)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $this->endpointPrefix);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_goals_import_month_with_get_return_200()
    {
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        (new HierarchyBuilder())->withUser($user)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $this->endpointPrefix);

        $response->assertStatus(Response::HTTP_OK);
    }
}

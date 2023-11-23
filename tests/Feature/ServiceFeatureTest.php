<?php

namespace TradeAppOne\Tests\Feature;

use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ServiceFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function get_should_return_status_service_available()
    {
        $statusConst = ConstantHelper::getAllConstants(ServiceStatus::class);

        $status = [];

        foreach ($statusConst as $key => $value) {
            array_push($status, [
                'slug'  => $key,
                "label" => trans("status.$key")
            ]);
        }

        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get('/service/status');

        $this->assertEquals($response->json()['all'], $status);
        $this->assertArrayHasKey('all', $response->json());
        $this->assertArrayHasKey('analytical', $response->json());
    }

}
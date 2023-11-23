<?php

namespace ClaroBR\Tests\Unit;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Connection\SivHttpClient;
use ClaroBR\Tests\Helpers\SivIntegrationHelper;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SivConnectionTest extends TestCase
{
    use SivIntegrationHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->app->bind(SivHttpClient::class, function () {
            $sivClient = $this->getMockBuilder(SivHttpClient::class)
                ->disableOriginalConstructor()
                ->setMethods(['authenticate'])
                ->getMock();
            return $sivClient;
        });
    }

    /** @test */
    public function authenticate_should_return_true_when_user_no_register_siv()
    {
        Auth::setUser($this->getUserWithSivCredentials());
        $connection = $this->app->make(SivConnectionInterface::class);

        $this->assertTrue($connection->authenticate());
    }

    /** @test */
    public function authenticate_should_return_true_when_user_is_passed_as_a_parameter()
    {
        $user       = (new UserBuilder())->build();
        $connection = $this->app->make(SivConnectionInterface::class);

        $this->assertTrue($connection->authenticate($user));
    }
}

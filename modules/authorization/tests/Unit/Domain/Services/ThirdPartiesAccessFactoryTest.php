<?php

namespace Authorization\tests\Unit\Domain\Services;

use Authorization\Tests\Helpers\Builders\ThirdPartyDatabaseBuilder;
use Authorization\Services\ThirdPartiesAccessFactory;
use Authorization\Services\ThirdPartyAccessDatabase;
use Mockery;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ThirdPartiesAccessFactoryTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_client_service(): void
    {
        $thirdPartiesAccess = new ThirdPartiesAccessFactory();
        $className          = get_class($thirdPartiesAccess);

        $this->assertEquals(ThirdPartiesAccessFactory::class, $className);
    }

    /** @test */
    public function should_return_one_client_when_exists(): void
    {
        $user             = (new UserBuilder())->build();
        $thirdPartyConfig = (new ThirdPartyDatabaseBuilder())
            ->withAccessKey("ACCESS_KEY")
            ->withAccessUser($user)
            ->build();

        app()->bind(ThirdPartyAccessDatabase::class, function () use ($thirdPartyConfig) {
            $thirdPartyAccessConfigMock = Mockery::mock(ThirdPartyAccessDatabase::class);
            $thirdPartyAccessConfigMock->shouldReceive('getByAccessKey')
                ->with('ACCESS_KEY')
                ->andReturn($thirdPartyConfig);

            return $thirdPartyAccessConfigMock;
        });

        $thirdPartiesAccess = new ThirdPartiesAccessFactory();
        $clientFound        = $thirdPartiesAccess->getByAccessKey("ACCESS_KEY");

        $this->assertNotNull($clientFound);
    }

    /** @test */
    public function should_return_none_client_when_not_exists(): void
    {
        $thirdPartiesAccess = new ThirdPartiesAccessFactory();

        $clientFound = $thirdPartiesAccess->getByAccessKey('invalid_access_key', 'invalid_key');

        $this->assertNull($clientFound);
    }
}

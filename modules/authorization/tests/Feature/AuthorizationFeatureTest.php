<?php

namespace Authorization\tests\Feature;

use Authorization\Exceptions\OriginNotFoundInWhiteListException;
use Authorization\Exceptions\RouteNotAvailableException;
use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Authorization\Services\ThirdPartyAccessDatabase;
use Authorization\tests\Helpers\Builders\ThirdPartyDatabaseBuilder;
use Illuminate\Http\Response;
use Mockery;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AuthorizationFeatureTest extends TestCase
{
    const HEADER = ThirdPartiesMiddleware::ACCESS_KEY;

    /** @test */
    public function should_return_response_status_200_when_request_has_valid_access_key_and_client()
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

        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, "ACCESS_KEY")
            ->json('GET', '/me');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_response_with_user_when_request_has_valid_access_key()
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

        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, "ACCESS_KEY")
            ->json('GET', '/me');

        $response->assertJsonFragment(['cpf' => $user->cpf]);
    }

    /** @test */
    public function should_return_response_403_with_error_when_access_not_in_whitelist()
    {
        $user             = (new UserBuilder())->build();
        $thirdPartyConfig = (new ThirdPartyDatabaseBuilder())
            ->withAccessKey("ACCESS_KEY")
            ->withAccessUser($user)
            ->withWhiteList("INVALID_WHITE_LIST")
            ->build();

        app()->bind(ThirdPartyAccessDatabase::class, function () use ($thirdPartyConfig) {
            $thirdPartyAccessConfigMock = Mockery::mock(ThirdPartyAccessDatabase::class);
            $thirdPartyAccessConfigMock->shouldReceive('getByAccessKey')
                ->with('ACCESS_KEY')
                ->andReturn($thirdPartyConfig);

            return $thirdPartyAccessConfigMock;
        });

        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, "ACCESS_KEY")
            ->json('GET', '/me');

        $response->assertSee(OriginNotFoundInWhiteListException::KEY);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }


    /** @test */
    public function should_return_response_403_with_error_when_route_not_in_routes_available()
    {
        $user             = (new UserBuilder())->build();
        $thirdPartyConfig = (new ThirdPartyDatabaseBuilder())
            ->withAccessKey("ACCESS_KEY")
            ->withAccessUser($user)
            ->withRoutes(collect([0 => ['uri' => 'me', 'method' => 'POST']]))
            ->build();

        app()->bind(ThirdPartyAccessDatabase::class, function () use ($thirdPartyConfig) {
            $thirdPartyAccessConfigMock = Mockery::mock(ThirdPartyAccessDatabase::class);
            $thirdPartyAccessConfigMock->shouldReceive('getByAccessKey')
                ->with('ACCESS_KEY')
                ->andReturn($thirdPartyConfig);

            return $thirdPartyAccessConfigMock;
        });

        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, "ACCESS_KEY")
            ->json('GET', '/me');

        $response->assertSee(RouteNotAvailableException::KEY);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }


    /** @test */
    public function should_return_response_status_401_with_invalid_credentials()
    {
        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, 'INVALID_CREDENTIALS')
            ->json('GET', '/me');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function should_return_response_invalid_credentials_when_access_key_invalid()
    {
        $response = $this
            ->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, 'INVALID_CREDENTIALS')
            ->json('GET', '/me');

        $expected = trans('messages.session_expired');
        $this->assertEquals($expected, $response->json('message'));
    }
}

<?php

namespace VivoBR\Tests\Feature;

use Illuminate\Http\Response;
use Mockery;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Connection\SunConnection;
use VivoBR\Tests\ServerTest\SunTestBook;

class VivoBrFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_vivobr_products_return_with_status_200(): void
    {
        $network    = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $userHelper = (new UserBuilder())->withNetwork($network)->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', '/vivobr/products');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_vivobr_products_return_with_status_200_when_filter_by_area_code(): void
    {
        $network    = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $userHelper = (new UserBuilder())->withNetwork($network)->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', '/vivobr/products', ['areaCode' => 11 ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_vivobr_products_return_with_status_421_when_not_exists_products(): void
    {
        $network    = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $userHelper = (new UserBuilder())->withNetwork($network)->build();

        $this->buildMockSunConnection('listPlans', "{}", 200);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', '/vivobr/products');

        $response->assertStatus(Response::HTTP_MISDIRECTED_REQUEST);
    }

    /** @test */
    public function should_return_true_when_customer_has_totalization(): void
    {
        $network     = (new NetworkBuilder())->withSlug('cea')->build();
        $user        = (new UserBuilder())->withNetwork($network)->build();
        $customerCpf = SunTestBook::CUSTOMER_TOTALIZATION;

        $this->authAs($user)
            ->json('GET', '/sales/sun/totalization/' . $customerCpf)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['message' => 'Ana Maria Serafim Nascimento, você Possui serviço fixa ativo.'])
            ->assertJsonFragment(['status' => true]);
    }

    /** @test */
    public function should_return_false_when_customer_has_not_totalization(): void
    {
        $network     = (new NetworkBuilder())->withSlug('cea')->build();
        $user        = (new UserBuilder())->withNetwork($network)->build();
        $customerCpf = '55328175700';

        $this->authAs($user)
            ->json('GET', '/sales/sun/totalization/' . $customerCpf)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['message' => 'Sergio Serafim Nascimento, você Não Possui serviço fixa ativo.'])
            ->assertJsonFragment(['status' => false]);
    }

    private function buildMockSunConnection(string $methodName, string $body, int $status): void
    {
        $response     = new \GuzzleHttp\Psr7\Response($status, ['Content­Type' => 'application/json'], $body);
        $restResponse = RestResponse::success($response);

        $mock = Mockery::mock(SunConnection::class)->makePartial();
        $mock->shouldReceive('selectCustomConnection')->andReturn($mock);
        $mock->shouldReceive($methodName)->andReturn($restResponse);

        $this->app->instance(SunConnection::class, $mock);
    }
}

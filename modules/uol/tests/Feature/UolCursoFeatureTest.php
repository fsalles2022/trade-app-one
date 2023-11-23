<?php

namespace ClaroBR\Tests\Feature;

use Gateway\API\Tokenization;
use Gateway\Connection\GatewayConnection;
use Gateway\Exceptions\GatewayExceptions;
use Gateway\tests\Helpers\GatewayFactoriesHelper;
use Gateway\tests\ServerTest\Methods\Sale;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Uol\Mail\CoursePurchased;
use Uol\Models\UolCurso;

class UolCursoFeatureTest extends TestCase
{
    use AuthHelper, GatewayFactoriesHelper;
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/uol/Factories'));
    }

    /** @test */
    public function should_return_response_with_status_201_and_a_correct_message_when_save_uol_courses()
    {
        $userHelper = (new UserBuilder())->build();
        $uolCurso   = $this->factory->of(UolCurso::class)->make();
        $payload    = ['pointOfSale' => $userHelper->pointsOfSale->first()->id, 'services' => [$uolCurso->toArray()]];
        $response   = $this
            ->withHeaders(['Authorization' => $this->loginUser($userHelper), 'client' => SubSystemEnum::WEB])
            ->post('sales', $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['messages' => trans('messages.sale_saved')]);
    }

    /** @test */
    public function should_return_response_with_status_200_and_a_valid_structure_when_activate_uol_courses()
    {
        Mail::fake();
        $userHelper = (new UserBuilder())->build();
        $uolCurso   = $this->factory->of(UolCurso::class)->make();
        $payload    = ['pointOfSale' => $userHelper->pointsOfSale->first()->id, 'services' => [$uolCurso->toArray()]];

        $createSale = $this->withHeaders(['Authorization' => $this->loginUser($userHelper), 'client' => SubSystemEnum::WEB])
            ->post('sales', $payload);

        $serviceTransaction = data_get($createSale->json(), 'data.sale.services.0.serviceTransaction');
        $payload            = [
            'creditCard'         => $this->payloadCreditCard(),
            'serviceTransaction' => $serviceTransaction
        ];

        $response = $this->withHeaders(['Authorization' => $this->loginUser($userHelper), 'client' => SubSystemEnum::WEB])
            ->put('sales', $payload);

        Mail::assertSent(CoursePurchased::class);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['message', 'activationCode']);
    }

    /** @test */
    public function should_return_with_status_422_and_a_correct_message_when_transaction_not_approved()
    {
        $userHelper = (new UserBuilder())->build();
        $uolCurso   = $this->factory->of(UolCurso::class)->make(['card' => ['token' => ['123456']]]);
        $payload    = ['pointOfSale' => $userHelper->pointsOfSale->first()->id, 'services' => [$uolCurso->toArray()]];
        $createSale = $this->withHeaders(['Authorization' => $this->loginUser($userHelper), 'client' => SubSystemEnum::WEB])
            ->post('sales', $payload);

        $mockToken = Mockery::mock(Tokenization::class)->makePartial();
        $mockToken->shouldReceive('getTokenCard')->andReturn('1234');

        $gatewayConn = Mockery::mock(GatewayConnection::class)->makePartial();
        $gatewayConn->shouldReceive('sale')->withAnyArgs()->andReturn((new Sale())->execute(9)); //9 is UNAUTHORIZED
        $gatewayConn->shouldReceive('tokenize')->andReturn($mockToken);

        $this->instance(GatewayConnection::class, $gatewayConn);

        $serviceTransaction = data_get($createSale->json(), 'data.sale.services.0.serviceTransaction');
        $payload            = [
            'creditCard'         => $this->payloadCreditCard(),
            'serviceTransaction' => $serviceTransaction
        ];
        $response           = $this->withHeaders(['Authorization' => $this->loginUser($userHelper), 'client' => SubSystemEnum::WEB])
            ->put('sales', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => trans('gateway::exceptions.' . GatewayExceptions::GATEWAY_TRANSACTION_NOT_APPROVED)]);
    }

    /** @test */
    public function get_should_return_plans_available()
    {
        $user     = (new UserBuilder())->build();
        $response = $this->authAs($user)->get('/uol/products');

        $response->assertJsonStructure(['*' => [
            'product', 'label', 'price', 'operator', 'operation', 'original']
        ]);
    }
}

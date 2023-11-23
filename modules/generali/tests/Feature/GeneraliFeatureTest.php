<?php


namespace Generali\tests\Feature;

use GA\Connections\GAClient;
use Generali\Adapters\Request\GeneraliActivationRequestAdapter;
use Generali\Assistance\Connection\GeneraliConnection;
use Generali\Assistance\Connection\GeneraliRoutes;
use Generali\Exceptions\GeneraliExceptions;
use Generali\Mail\InsuranceActivation;
use Generali\Models\Generali;
use Generali\Models\GeneraliProduct;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GeneraliFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test*/
    public function should_return_pdf_insurance_ticket(): void
    {
        $user        = (new UserBuilder())->build();
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $service     = factory(Generali::class)->create();

        $sale = (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();

        $service->setRelation('sale', $sale);

        $response = $this->authAs($user)
            ->post('/generali/v1/insurance', ['serviceTransaction' => $service->serviceTransaction])
            ->assertStatus(Response::HTTP_OK);

        $response->assertHeader('content-type', 'text/html; charset=UTF-8');
        $this->assertContains('PDF', $response->content());
    }

    /** @test*/
    public function should_return_exception_when_pdf_no_created(): void
    {
        $user    = (new UserBuilder())->build();
        $service = factory(Generali::class)->make();

        $response = $this->authAs($user)
            ->post('/generali/v1/insurance', ['serviceTransaction' => $service->serviceTransaction])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment([
            'message' => trans('generali::exceptions.' . GeneraliExceptions::INSURANCE_TICKET_NOT_CREATED)
        ]);
    }

    /** @test*/
    public function should_return_201_when_save_generali_sale(): void
    {
        $network = factory(Network::class)
            ->create(['availableServices' => '{"INSURANCE":{"GENERALI":["GENERALI_ELECTRONICS"]}}']);

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $service     = factory(Generali::class)->make()->toArray();
        $payload     = ['pointOfSale' => $pointOfSale->id, 'services' => [$service]];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->post('sales', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['messages' => trans('messages.sale_saved')]);

        $this->assertDatabaseHas('sales', [
            'services.customer.cpf' => data_get($payload, 'services.0.customer.cpf'),
            'services.device.imei'  => data_get($payload, 'services.0.device.imei'),
            'total'  => 95.82,
        ], 'mongodb');
    }

    /** @test*/
    public function should_return_a_correct_structure_of_sale(): void
    {
        $network =  factory(Network::class)
            ->create(['availableServices' => '{"INSURANCE":{"GENERALI":["GENERALI_ELECTRONICS"]}}']);

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $service     = factory(Generali::class)->make()->toArray();
        $payload     = ['pointOfSale' => $pointOfSale->id, 'services' => [$service]];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->post('sales', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['messages' => trans('messages.sale_saved')])
            ->assertJsonStructure([
            'data' => [
                'sale' => [
                    'services' => [
                        '*' => [
                            'premium' => [
                                'total'
                            ]
                        ]
                    ]
                ]
            ]
            ]);
    }

    /** @test*/
    public function should_return_422_and_message_when_network_has_no_permission(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->doNotPopulateServices()->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $service     = factory(Generali::class)->make()->toArray();
        $payload     = ['pointOfSale' => $pointOfSale->id, 'services' => [$service]];

        $this->withHeaders(['Authorization' => $this->loginUser($user), 'client' => SubSystemEnum::WEB])
            ->post('sales', $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment(['message' => trans('validation.has_operator_permission')]);
    }

    /** @test*/
    public function should_return_201_when_active_generali_sale(): void
    {
        Mail::fake();
        $network =  factory(Network::class)
            ->create(['availableServices' => '{"INSURANCE":{"GENERALI":["GENERALI_ELECTRONICS"]}}']);

        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $service     = factory(Generali::class)->create();

        $service->status = ServiceStatus::PENDING_SUBMISSION;

        $sale = (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();

        $payload = [
            'serviceTransaction' => data_get($sale->toArray(), 'services.0.serviceTransaction'),
            'creditCard' => [
                'flag' => 'visa',
                'name' => 'Name Card',
                'cardNumber' => '4539934475836807',
                'cvv' => '135',
                'month' => '10',
                'year' => '25',
                'times' => 6,
            ]
        ];

        $this->authAs($user)
            ->withHeader('client', SubSystemEnum::WEB)
            ->put('sales', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['message' => trans('generali::messages.service_activated')]);

        Mail::assertSent(InsuranceActivation::class);
    }

    /** @test*/
    public function should_return_structure_gateway_transaction(): void
    {
        $service            = factory(Generali::class)->create();
        $generaliConnection = resolve(GeneraliConnection::class);

        $transaction = $generaliConnection->getTransactionByReference($service->serviceTransaction)['data'];

        $this->assertInternalType('array', $transaction);
        $this->assertArrayHasKey('service', $transaction);
        $this->assertArrayHasKey('customer', $transaction);
        $this->assertArrayHasKey('client', $transaction);
        $this->assertArrayHasKey('payment', $transaction);
        $this->assertArrayHasKey('license', $transaction['service']);
    }

    /** @test*/
    public function should_return_gateway_subscription_structure(): void
    {
        $service = factory(Generali::class)->create();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $service->setRelation('sale', $sale);

        $service->status = ServiceStatus::PENDING_SUBMISSION;

        $gaClient     = resolve(GAClient::class);
        $adaptPayload = GeneraliActivationRequestAdapter::adapt($service);
        $response     = $gaClient->post(GeneraliRoutes::activate(), $adaptPayload);

        $this->assertJson($response->get(), 'Seguro Roubo, Furto Qualificado.');
        $this->assertArrayHasKey('data', $response->toArray());
    }

    /** @test*/
    public function should_return_products_available(): void
    {
        factory(GeneraliProduct::class)->create();
        $user = (new UserBuilder())->build();

        $this->authAs($user)
            ->get(GeneraliRoutes::eligibility() . '?devicePrice=750.00&deviceDate=2019-12-12T00:00:00-03:00&slug=SMARTPHONE')
            ->assertJsonFragment(['produto_parceiro_id' => '130', 'produto_parceiro_id' => '131', 'produto_parceiro_id' => '132'])
            ->assertJsonStructure([
                '*' => [
                    'produto_parceiro_id',
                    'nome',
                    'slug_produto',
                    'plans' => [ '*' => [
                        'produto_parceiro_plano_id',
                        'slug_plano',
                        'valor_premio_bruto',
                        ]
                    ],
                ]
            ])->assertJsonCount(3);
    }


    /** @test*/
    public function should_return_correct_interest_structure(): void
    {
        $service = factory(Generali::class)->create();
        $sale    = (new SaleBuilder())->withServices([$service])->build();

        $response = $this->authAs()
            ->json('GET', '/generali/v1/interest', [
                'serviceTransaction' => $sale->services()->first()->serviceTransaction
            ]);

        $response->assertJsonStructure([
            '*' => [
                'price',
                'times',
                'interest'
            ]
        ]);

        $response->assertJsonCount(12);
    }
}

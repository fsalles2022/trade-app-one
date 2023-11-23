<?php

namespace Buyback\Tests\Feature;

use Buyback\Models\Quiz;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Buyback\Tests\Helpers\TradeInServices;
use Faker\Generator;
use Faker\Provider\PhoneNumber;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Services\ServiceService;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BuybackSaleAssistanceFeatureTest extends TestCase
{
    use AuthHelper, BindInstance;

    protected $endpointDevices     = 'buyback/devices';
    protected $endPointQuestions   = 'buyback/questions';
    protected $endpointPrice       = 'buyback/price';
    protected $endpointSale        = 'sales';
    protected $endpointRevaluation = 'buyback/revaluation';
    protected $endpointVoucher     = 'buyback/voucher';

    /** @test */
    public function get_should_response_with_status_200_when_appraiser_evaluate(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpointSale, $payload);

        $serviceTransaction     = data_get($response->json(), 'data.sale.services.0.serviceTransaction');
        $questions              = data_get($response->json(), 'data.sale.services.0.evaluations.salesman.questions');
        $questions[0]['answer'] = 0;
        unset($questions[0]['network']);

        $this->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('PUT', '/' . $this->endpointSale, ['serviceTransaction' => $serviceTransaction]);

        $appraiserPayload = [
            'serviceTransaction' => $serviceTransaction,
            'questions'          => $questions
        ];

        $responseEvaluate = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointRevaluation, $appraiserPayload);

        $appraiserPrice = data_get($responseEvaluate->json(), 'data.evaluations.appraiser.price');
        $salePrice      = data_get($responseEvaluate->json(), 'data.price');

        $this->assertEquals($appraiserPrice, $salePrice);
        $responseEvaluate->assertStatus(Response::HTTP_CREATED);
        $responseEvaluate->assertJsonFragment(['message' => trans('buyback::messages.evaluation_success')]);
        $responseEvaluate->assertJsonStructure([
            'data' => [
                'evaluations' => [
                    'appraiser' => [
                        'price',
                        'deviceNote',
                        'questions' => [
                            '*' => [
                                'id',
                                'question',
                                'weight',
                                'answer',
                                'blocker'
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $responseRevaluationAgain = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointRevaluation, $appraiserPayload);

        $responseRevaluationAgain->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $responseRevaluationAgain->assertJsonFragment(['message' => trans('buyback::exceptions.revaluation_already_done_exception.message')]);
    }

    private function createBuybackPayload($networkEntity, $pointOfSale, $service = null): array
    {
        $device   = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())
            ->withNetwork($networkEntity)
            ->withQuiz($quiz)
            ->withStates(['non_blocker'])
            ->build();

        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($networkEntity)->build();

        $serviceBuyback = is_null($service) ? TradeInServices::SaldaoInformaticaMobile() : $service;

        $payloadExtra                           = [];
        $payloadExtra['deviceId']               = $device->id;
        $payloadExtra['networkId']              = $networkEntity->id;
        $payloadExtra['questions']              = [$question->toArray()];
        $payloadExtra['questions'][0]['answer'] = 1;
        $payloadExtra['imei']                   = (new PhoneNumber((new Generator())))->imei();

        return [
            'pointOfSale' => $pointOfSale->id,
            'services'    => [array_merge($serviceBuyback->toArray(), $payloadExtra)]
        ];
    }

    /** @test */
    public function get_should_response_with_status_422_when_buyback_not_has_imei(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);
        unset($payload['services'][0]['imei']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpointSale, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_422_when_buyback_not_has_deviceId(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);
        unset($payload['services'][0]['deviceId']);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpointSale, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_200_when_buyback_saved(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('POST', '/' . $this->endpointSale, $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(["messages" => "Venda salva com sucesso"]);
    }

    /** @test */
    public function get_should_response_with_status_200_when_activated(): void
    {
        $helper             = $this->createAndActiveSaleTradeIn();
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $responseActivate = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('PUT', '/' . $this->endpointSale, ['serviceTransaction' => $serviceTransaction]);

        $responseActivate->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function put_should_response_with_status_200_when_canceled_voucher(): void
    {
        $permission = factory(Permission::class)
            ->create(['slug' => SalePermission::getFullName(PermissionActions::CANCEL)]);

        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->post($this->endpointSale, $payload);

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');

        $serviceService = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $responseCancellation = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put('/sales/cancel', ['serviceTransaction' => $serviceTransaction]);

        $responseCancellation->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function put_should_response_with_status_422_when_voucher_is_already_canceled(): void
    {
        $permission = factory(Permission::class)
            ->create(['slug' => SalePermission::getFullName(PermissionActions::CANCEL)]);

        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->post($this->endpointSale, $payload);

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');

        $serviceService = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $cancelVoucher1 = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put('/sales/cancel', ['serviceTransaction' => $serviceTransaction]);

        $cancelVoucher2 = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put('/sales/cancel', ['serviceTransaction' => $serviceTransaction]);

        $cancelVoucher2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function put_should_response_with_status_422_when_try_activate_voucher_canceled(): void
    {
        $permission = factory(Permission::class)
            ->create(['slug' => SalePermission::getFullName(PermissionActions::CANCEL)]);

        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermissions([$permission])->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->post($this->endpointSale, $payload);

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');
        $serviceService     = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $cancelVoucher = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put('/sales/cancel', ['serviceTransaction' => $serviceTransaction]);

        $activeVoucher = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put($this->endpointSale, ['serviceTransaction' => $serviceTransaction]);

        $activeVoucher->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_200_when_generate_voucher(): void
    {
        $helper             = $this->createAndActiveSaleTradeIn();
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent')->once();

        $responseActivate = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('PUT', '/' . $this->endpointSale, ['serviceTransaction' => $serviceTransaction]);

        $responseActivate->assertStatus(Response::HTTP_CREATED);

        $responseVoucher = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointVoucher, ['serviceTransaction' => $serviceTransaction]);

        $responseVoucher->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_200_when_generate_voucher_for_iplace(): void
    {
        $serviceIplace      = TradeInServices::IplaceMobile();
        $helper             = $this->createAndActiveSaleTradeIn($serviceIplace);
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent')->once();

        $responseActivate = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('PUT', '/' . $this->endpointSale, ['serviceTransaction' => $serviceTransaction]);

        $responseActivate->assertStatus(Response::HTTP_CREATED);

        $responseVoucher = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointVoucher, ['serviceTransaction' => $serviceTransaction]);

        $responseVoucher->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_422_when_try_generate_voucher_is_canceled(): void
    {
        $helper             = $this->createAndActiveSaleTradeIn();
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $this->bindInstance(PdfHelper::class)->shouldReceive('fromHtmlToContent');

        $responseCancel = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put($this->endpointVoucher.'/cancel', ['serviceTransaction' => $serviceTransaction]);

        $responseVoucher = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointVoucher, ['serviceTransaction' => $serviceTransaction]);

        $responseVoucher->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function put_should_return_401_when_user_not_permission_to_cancel(): void
    {
        $helper             = $this->createAndActiveSaleTradeIn();
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $serviceService = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $responseCancellation = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put('/sales/cancel', ['serviceTransaction' => $serviceTransaction]);

        $responseCancellation->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function put_should_return_status_200_when_burned_voucher(): void
    {
        $serviceIplace      = TradeInServices::IplaceMobile();
        $helper             = $this->createAndActiveSaleTradeIn($serviceIplace);
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $serviceService = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put($this->endpointVoucher.'/burn', ['serviceTransaction' => $serviceTransaction]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function put_should_return_status_422_when_service_not_belongs_to_operation_burnerVoucher(): void
    {
        $helper             = $this->createAndActiveSaleTradeIn();
        $serviceTransaction = $helper['serviceTransaction'];
        $userHelper         = $helper['userHelper'];

        $serviceService = resolve(ServiceService::class);

        $serviceService->editStatusByContext($serviceTransaction, ServiceStatus::ACCEPTED);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->put($this->endpointVoucher.'/burn', ['serviceTransaction' => $serviceTransaction]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function createAndActiveSaleTradeIn($service = null): array
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $payload = $this->createBuybackPayload($networkEntity, $pointOfSale, $service);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->post($this->endpointSale, $payload);

        $serviceTransaction = data_get($response->json(), 'data.sale.services.0.serviceTransaction');
        return [
            'serviceTransaction' => $serviceTransaction,
            'userHelper'               => $userHelper
        ];
    }
}

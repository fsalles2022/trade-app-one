<?php

namespace Buyback\tests\Feature;

use Buyback\Exportables\Sales\BuybackExport;
use Buyback\Models\Quiz;
use Buyback\Services\OfferDeclinedService;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Buyback\Tests\Helpers\Builders\OfferDeclinedBuilder;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Faker\Generator;
use Faker\Provider\PhoneNumber;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Reports\Tests\Unit\Exportables\BuybackExportFixture;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BuybackFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endPointDevices       = 'buyback/devices';
    protected $endPointQuestions     = 'buyback/questions';
    protected $endpointPrice         = 'buyback/price';
    protected $endpointSale          = 'sales';
    protected $endpointEvaluate      = 'buyback/evaluate';
    protected $endPointPrice         = 'buyback/price';
    protected $endPointOfferDeclined = 'buyback/offer_declined';

    /** @test */
    public function get_should_response_with_status_200_in_devices_list()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        (new DeviceBuilder())->withNetwork($networkEntity)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointDevices);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_422_in_devices_list_when_collection_response_is_empty()
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointDevices);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_422_when_device_is_from_another_network()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();

        $anotherNetwork = (new NetworkBuilder())->build();
        $quiz           = factory(Quiz::class)->create();
        $evaluation     = (new EvaluationBuilder())->withQuiz($quiz)->withNetwork($anotherNetwork)->build();
        (new QuestionBuilder())->withQuiz($quiz)->withNetwork($anotherNetwork)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointQuestions . '?deviceId=' . $evaluation->deviceNetworkId);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_200_in_questions_list()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $evaluation    = (new EvaluationBuilder())->withQuiz($quiz)->withNetwork($networkEntity)->build();
        (new QuestionBuilder())->withQuiz($quiz)->withNetwork($networkEntity)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointQuestions . '?deviceId=' . $evaluation->deviceNetworkId);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_422_in_questions_list_when_device_not_found()
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointQuestions . '?deviceId=1');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_422_in_questions_list_when_evaluation_not_found()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        (new DeviceBuilder())->withNetwork($networkEntity)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointQuestions . '?deviceId=1');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_422_in_questions_list_when_questions_not_found()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        $evaluation    = (new EvaluationBuilder())->withNetwork($networkEntity)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endPointQuestions . '?deviceId=' . $evaluation->deviceNetworkId);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function get_should_response_with_status_200_in_price()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        $device        = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $question      = (new QuestionBuilder())->withNetwork($networkEntity)->withQuiz($quiz)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($networkEntity)->build();
        $payload = [
            "deviceId" => $device->id,
            "questions" => [
                [
                    "id" => $question->id,
                    "answer" => 1
                ]
            ]
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpointPrice, $payload);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_status_200_in_offer_declined()
    {
        $networkEntity = (new NetworkBuilder())->build();
        $userHelper    = (new UserBuilder())->withNetwork($networkEntity)->build();
        $device        = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $question      = (new QuestionBuilder())->withNetwork($networkEntity)->withQuiz($quiz)->build();

        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($networkEntity)->build();

        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);

        $payload = [
            "customer" => [
                "fullName" => 'João das Neves Correa',
                "email" => 'joao.neves@teste.com'
            ],
            "device" => [
                "id" => $device->id,
                "imei" => (new PhoneNumber((new Generator())))->imei()
            ],
            "questions" => [
                [
                    "id" => $question->id,
                    "answer" => 1
                ]
            ],
            "reason" => "Cliente achou o preço muito baixo",
            "operator" => Operations::TRADE_IN_MOBILE,
            "operation" => Operations::SALDAO_INFORMATICA,
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->postJson('/' . $this->endPointOfferDeclined, $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'operator',
            'operation',
            'customer' => [
                'fullName',
                'email'
            ],
            'device' => [
                'id',
                'label',
                'model',
                'brand',
                'color',
                'storage',
                'imageFront',
                'imageBehind',
                'price',
                'note',
                'imei'
            ],
            'questions',
            'reason',
            'pointOfSale',
            'user'
        ]);
    }

    /** @test */
    public function declined_offers_should_response_with_status_200_and_a_valid_structure_when_there_are_withdrawals()
    {
        $userHelper = (new UserBuilder())->build();
        (new OfferDeclinedBuilder())->withUser($userHelper)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->getJson('buyback/offer_declined');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                [
                    'customer' => [
                        'fullName',
                    ],
                    'device' => [
                        'label',
                        'price',
                        'note'
                    ],
                    'questions',
                    'reason',
                    'pointOfSale',
                    'user',
                ]
            ]
        ]);
    }

    /** @test */
    public function get_return_status_200_and_analytical_unified()
    {
        $user = (new UserBuilder())->build();
        (new OfferDeclinedBuilder())->withUser($user)->build();

        $repository = \Mockery::mock(SaleReportRepository::class)
            ->makePartial();
        $repository
            ->shouldReceive('getFilteredByContextUsingScroll')
            ->andReturn(collect(BuybackExportFixture::fixtureFromElastic()));
        $offerDeclined = resolve(OfferDeclinedService::class);
        $saleService   = resolve(SaleService::class);

        $this->app->instance(BuybackExport::class, new BuybackExport($repository, $offerDeclined, $saleService));

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get('analytical_report/trade-in-unified');

        $response->assertStatus(Response::HTTP_OK);
        self::assertContains("DESISTENCIA", $response->content());
        self::assertContains("VENDA", $response->content());
    }

    /** @test */
    public function post_return_price_note_and_state_device()
    {
        $network  = (new NetworkBuilder())->build();
        $device   = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();

        $payload = [
            "deviceId" => $device->id,
            "questions" => [
                [
                    'id' => $question->id,
                    'answer' => 1
                ]
            ]
        ];

        $user     = (new UserBuilder())->withNetwork($network)->build();
        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post('buyback/price', $payload);

        $response->assertJsonStructure(["price", "note", "tierNote"]);
    }
}

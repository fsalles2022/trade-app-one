<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Assistance\TradeInSaleAssistance;
use Buyback\Enumerators\EvaluationStatus;
use Buyback\Exceptions\RevaluationAlreadyDoneException;
use Buyback\Exceptions\TradeInExceptions;
use Buyback\Models\Question;
use Buyback\Models\Quiz;
use Buyback\Services\TradeInService;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Buyback\Tests\Helpers\TradeInServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mockery;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TradeInSaleAssistanceTest extends TestCase
{
    /** @test */
    public function should_revaluation_update_price_with_invalid_status(): void
    {
        $saleServiceMock = Mockery::mock(SaleService::class)->makePartial();
        $saleServiceMock->shouldReceive('findService')->once()->andReturn((new Service()));

        $question = (new QuestionBuilder())->build();
        $tradeIn  = new TradeInSaleAssistance($saleServiceMock, resolve(TradeInService::class));
        $user     = new User();
        Auth::login($user);

        $this->expectException(RevaluationAlreadyDoneException::class);
        $parameters = self::getParameters('12345', $question, EvaluationStatus::APPRAISER);

        $tradeIn->revaluation($parameters);
    }

    /** @test */
    public function should_save_sale_when_imei_already_exists_in_accepted_sale_for_trade_in(): void
    {
        $service = TradeInServices::SaldaoInformaticaMobile(['status' => ServiceStatus::ACCEPTED]);
        (new SaleBuilder())->withServices([$service])->build();

        $newService = factory(Service::class)->make(['device' => ['imei' => $service['imei']]]);

        resolve(TradeInSaleAssistance::class)->integrateService($newService, []);

        $this->assertDatabaseHas('services', ['imei' => $service['imei']], 'mongodb');
    }

    /** @test */
    public function should_throw_exception_when_imei_already_exists_in_canceled_sale(): void
    {
        $newService = factory(Service::class)->make(['status' => ServiceStatus::CANCELED]);

        $this->expectExceptionMessage(trans('buyback::exceptions.' . TradeInExceptions::VOUCHER_ALREADY_CANCELED));

        resolve(TradeInSaleAssistance::class)->integrateService($newService, []);
    }

    /** @test */
    public function should_allow_persist_sale_when_imei_exist_in_pending_submission(): void
    {
        $sale = TradeInServices::SaldaoInformaticaMobile([
            'status' => ServiceStatus::PENDING_SUBMISSION
        ]);
        (new SaleBuilder())->withServices([$sale])->build();

        $newService = factory(Service::class)->make([
            'device' => [
                'imei' => $sale->device['imei']
            ]
        ]);

        $tradeInService = resolve(TradeInSaleAssistance::class);

        $result = $tradeInService->integrateService($newService, []);

        $this->assertInstanceOf(JsonResponse::class, $result);
    }

    /** @test */
    public function should_revaluation_when_service_status_accepted_produce_evaluation(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $deviceEntity  = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $question      = (new QuestionBuilder())->withNetwork($networkEntity)->withQuiz($quiz)->build();
        $evaluation    = (new EvaluationBuilder())->withQuiz($quiz)->withDevice($deviceEntity)->withNetwork($networkEntity)
            ->build();

        $service = TradeInServices::SaldaoInformaticaMobile([
            'status' => ServiceStatus::ACCEPTED,
            'evaluationsValues' => $evaluation->toArray()
        ]);

        (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();

        $tradeIn = new TradeInSaleAssistance(resolve(SaleService::class), resolve(TradeInService::class));
        $user    = (new UserBuilder())->build();
        Auth::login($user);

        $parameters = self::getParameters($service->serviceTransaction, $question, EvaluationStatus::APPRAISER);

        $tradeIn->revaluation($parameters);
    }

    /** @test */
    public function should_revaluation_when_service_not_has_evaluation_values(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $deviceEntity  = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $question      = (new QuestionBuilder())->withNetwork($networkEntity)->withQuiz($quiz)->build();
        (new EvaluationBuilder())->withQuiz($quiz)->withDevice($deviceEntity)->withNetwork($networkEntity)
            ->build();

        $service = TradeInServices::SaldaoInformaticaMobile([
            'status' => ServiceStatus::ACCEPTED,
        ]);

        (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();

        $tradeIn = new TradeInSaleAssistance(resolve(SaleService::class), resolve(TradeInService::class));
        $user    = (new UserBuilder())->build();
        Auth::login($user);

        $parameters = self::getParameters($service->serviceTransaction, $question, EvaluationStatus::APPRAISER);

        $tradeIn->revaluation($parameters);
    }

    /** @test */
    public function should_revaluation_calculate_price_based_on_instant_sale(): void
    {
        $networkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($networkEntity)->build();
        $deviceEntity  = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz          = factory(Quiz::class)->create();
        $question      = (new QuestionBuilder())->withNetwork($networkEntity)->withQuiz($quiz)->build();
        $evaluation    = (new EvaluationBuilder())->withQuiz($quiz)->withDevice($deviceEntity)->withNetwork($networkEntity)->build();

        $service                = TradeInServices::SaldaoInformaticaMobile([
            'status' => ServiceStatus::ACCEPTED,
            'evaluationsValues' => $evaluation->toArray()
        ]);
        $goodValueOnSaleInstant = $evaluation->goodValue;

        //Update Price of goodValue
        $evaluation->goodValue = 9999;
        $evaluation->save();

        (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();

        $tradeIn = new TradeInSaleAssistance(resolve(SaleService::class), resolve(TradeInService::class));
        $user    = (new UserBuilder())->build();
        Auth::login($user);

        $parameters = self::getParameters($service->serviceTransaction, $question, EvaluationStatus::APPRAISER);
        $result     = $tradeIn->revaluation($parameters);

        $this->assertEquals($result['evaluations']['appraiser']['price'], $goodValueOnSaleInstant);
    }

    private static function getParameters(string $serviceTransaction, Question $question, string $evaluationType): array
    {
        return [
            'serviceTransaction' =>  $serviceTransaction,
            'questions' => [[
                'id' => $question->id,
                'networkId' => $question->networkId,
                'answer' => 1
            ]],
            'evaluationType' => $evaluationType
        ];
    }
}

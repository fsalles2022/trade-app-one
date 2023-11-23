<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Exceptions\TradeInExceptions;
use Buyback\Helpers\SumQuestionsWeight;
use Buyback\Models\Quiz;
use Buyback\Services\TradeInService;
use Buyback\Services\DeviceService;
use Buyback\Services\MountNewAttributesFromTradeIn;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Faker\Generator;
use Faker\Provider\PhoneNumber;
use Illuminate\Support\Facades\DB;
use Buyback\Tests\Helpers\Builders\DeviceTierBuilder;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class MountNewAttributesFromTradeInTest extends TestCase
{
    use ArrayAssertTrait;

    /** @test */
    public function should_return_an_instance()
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $className = get_class($class);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }

    /** @test
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function should_return_an_exception_when_service_not_has_imei(): void
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $this->expectException(ModelInvalidException::class);
        $className = $class->getAttributes([
                'deviceId' => 'required',
                'networkId' => 'required',
                'questions' => 'required'
            ]);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }

    /** @test
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function should_return_an_exception_when_device_network_not_exists(): void
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $this->expectExceptionMessage(trans('buyback::exceptions.' . TradeInExceptions::DEVICE_NOT_BELONG_TO_NETWORK));
        $className = $class->getAttributes([
                'deviceId' => 1,
                'imei' => 'required',
                'networkId' => 1,
                'questions' => ['question' => '']
            ]);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }

    /** @test */
    public function should_return_when_sku_not_exists_id_of_device(): void
    {
        $class         = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );
        $networkEntity = (new NetworkBuilder())->build();

        $device   = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())
            ->withNetwork($networkEntity)
            ->withQuiz($quiz)
            ->withStates(['non_blocker'])
            ->build();

        (new DeviceTierBuilder())->build();
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($networkEntity)->build();

        $tradeServiceResult = $class->getAttributes(
            [
                'imei' => (new PhoneNumber((new Generator())))->imei(),
                'deviceId' => $device->id,
                'networkId' => $networkEntity->id,
                'questions' => [$question->toArray()],
            ]
        );

        $this->assertEquals($device->id, $tradeServiceResult['device']['sku']);
    }

    /** @test */
    public function should_return_structured_trade_in_with_correct_values(): void
    {
        $sku           = uniqid();
        $networkEntity = (new NetworkBuilder())->build();
        factory(Device::class, 5)->create();
        $devices    = (new DeviceBuilder())->withSku($sku)->withNetwork($networkEntity)->generateDeviceTimes(10);
        $deviceImei = (new PhoneNumber((new Generator())))->imei();
        factory(Quiz::class, 5)->create();
        $quiz = factory(Quiz::class)->create();
        (new QuestionBuilder())->generateQuestionTimes(10);

        $questions = (new QuestionBuilder())
            ->withNetwork($networkEntity)
            ->withQuiz($quiz)
            ->withStates(['non_blocker'])
            ->generateQuestionTimes(5);

        $questionsArray = [];
        $questions->sortBy('order')->each(static function ($question) use (&$questionsArray) {
            $questionsArray[] = [
                'id' => (int) $question->id,
                'question' => $question->question,
                'weight' => (string) $question->weight,
                'order' => (string) $question->order,
                'blocker' => (int) $question->blocker,
                'description' => $question->description,
                'answer' => true
            ];
        });

        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
        (new EvaluationBuilder())->generateEvaluationTimes(5);

        $deviceIndex = random_int(0, 9);
        $evaluation  = (new EvaluationBuilder())->withDevice($devices[$deviceIndex])->withQuiz($quiz)->withNetwork($networkEntity)->build();

        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $tradeServiceResult = $class->getAttributes(
            [
                'imei' => $deviceImei,
                'deviceId' => $devices[$deviceIndex]->id,
                'networkId' => $networkEntity->id,
                'questions' => $questionsArray,
            ]
        );

        $this->assertEquals(
            $tradeServiceResult,
            [
                'device' => [
                    'id' => $devices[$deviceIndex]->id,
                    "label" => $devices[$deviceIndex]->label,
                    "model" => $devices[$deviceIndex]->model,
                    "brand" => $devices[$deviceIndex]->brand,
                    "color" => $devices[$deviceIndex]->color,
                    "storage" => $devices[$deviceIndex]->storage,
                    "createdAt" => $devices[$deviceIndex]->createdAt,
                    "updatedAt" => $devices[$deviceIndex]->updatedAt,
                    "deletedAt" => $devices[$deviceIndex]->deletedAt,
                    "imei" => $deviceImei,
                    "sku" => $sku
                ],
                'label' => $devices[$deviceIndex]->label,
                'price' => (float) $evaluation->goodValue,
                'evaluationsValues' => [
                    "id" => $evaluation->id,
                    "goodValue" => $evaluation->goodValue,
                    "averageValue" => $evaluation->averageValue,
                    "defectValue" => $evaluation->defectValue,
                ],
                'evaluations' => [
                    "salesman" => [
                        "price" => (float) $evaluation->goodValue,
                        "deviceNote" => SumQuestionsWeight::getWeightSum($questionsArray, $questionsArray),
                        "questions" => $questionsArray
                    ]
                ],
            ]
        );
    }

    /** @test
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function should_return_an_exception_when_service_not_has_deviceId(): void
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $this->expectException(ModelInvalidException::class);
        $className = $class->getAttributes([
                'imei' => 'f',
                'networkId' => 'required',
                'questions' => 'required'
            ]);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }

    /** @test
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function should_return_an_exception_when_service_not_has_network_id(): void
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $this->expectException(ModelInvalidException::class);
        $className = $class->getAttributes([
                'deviceId' => 'f',
                'imei' => 'f',
                'questions' => 'required'
            ]);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }

    /** @test
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function should_return_an_exception_when_service_not_has_questions(): void
    {
        $class = new MountNewAttributesFromTradeIn(
            resolve(DeviceService::class),
            resolve(TradeInService::class)
        );

        $this->expectException(ModelInvalidException::class);
        $className = $class->getAttributes([
                'deviceId' => 'f',
                'imei' => 'f',
                'networkId' => 'required',
            ]);

        $this->assertEquals(MountNewAttributesFromTradeIn::class, $className);
    }
}

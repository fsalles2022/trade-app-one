<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Models\Quiz;
use Buyback\Services\DeviceRated;
use Buyback\Services\TradeInService;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Buyback\Tests\Helpers\Builders\QuestionBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class TradeInServiceTest extends TestCase
{
    const CHOICED_INDEX = 3;

    /** @test */
    public function should_return_an_instance_of_buyback_service()
    {
        $tradeInService = resolve(TradeInService::class);
        $className      = get_class($tradeInService);
        $this->assertEquals(TradeInService::class, $className);
    }

    /** @test */
    public function should_return_collection_when_call_get_quiz_questions()
    {
        $tradeInService = resolve(TradeInService::class);
        $network        = (new NetworkBuilder())->build();
        $device         = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz           = factory(Quiz::class)->create();
        (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();
        $quizQuestions = $tradeInService->getQuestions($device->id, $network->id);
        $this->assertInstanceOf(Collection::class, $quizQuestions);
    }

    /** @test */
    public function should_return_array_with_device_price_and_device_note_when_call_get_price()
    {
        $tradeInService = resolve(TradeInService::class);
        $network        = (new NetworkBuilder())->build();
        $device         = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz           = factory(Quiz::class)->create();
        $question       = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();
        $answer      = array([
            "id" => $question->id,
            "answer" => 1
        ]);
        $devicePrice = $tradeInService->fetchQuestionsWithWeight($device->id, $network->id, $answer);
        $this->assertInternalType('int', $devicePrice);
    }

    /** @test */
    public function should_return_correct_evaluation_price_note_and_state_when_call_get_price()
    {
        $tradeInService = resolve(TradeInService::class);
        $network        = (new NetworkBuilder())->build();
        factory(Device::class, 10)->create();
        $devices     = (new DeviceBuilder())->withNetwork($network)->generateDeviceTimes(10);
        $quiz        = factory(Quiz::class)->create();
        $note        = 10;
        $question    = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->withWeight($note)->build();
        $evaluations = collect();
        foreach ($devices->shuffle() as $device) {
            $evaluations->push((new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build());
        }
        $questions           = [
            [
                'id' => $question->id,
                'answer' => 1
            ]
        ];
        $pivotDevicesNetwork = DB::table('devices_network')
            ->where(['networkId' => $network->id, 'deviceId' => $devices[self::CHOICED_INDEX]->id])
            ->first();
        $goodValue           = $evaluations->where('deviceNetworkId', $pivotDevicesNetwork->id)->first()->goodValue;

        $rating = $tradeInService->getPrice($devices[self::CHOICED_INDEX]->id, $network->id, $questions);

        $this->assertInstanceOf(DeviceRated::class, $rating);
        $this->assertEquals($rating->price, $goodValue);
        $this->assertEquals($rating->note, $note);
        $this->assertEquals($rating->tierNote, trans('buyback::messages.device_states.good'));
    }

    /** @test */
    public function should_return_correct_questions_when_there_is_more_than_one_quiz()
    {
        $tradeInService         = resolve(TradeInService::class);
        $firstNetwork           = (new NetworkBuilder())->build();
        $secondNetwork          = (new NetworkBuilder())->build();
        $quizFirstNetwork       = factory(Quiz::class)->create();
        $quizSecondNetwork      = factory(Quiz::class)->create();
        $questionsFirstNetwork  = (new QuestionBuilder())->withNetwork($firstNetwork)->withQuiz($quizFirstNetwork)->generateQuestionTimes(10);
        $questionsSecondNetwork = (new QuestionBuilder())->withNetwork($secondNetwork)->withQuiz($quizSecondNetwork)->generateQuestionTimes(10);

        $device = (new DeviceBuilder())->withNetwork($firstNetwork)->build();
        (new EvaluationBuilder())->withNetwork($firstNetwork)->withQuiz($quizFirstNetwork)->withDevice($device)->build();

        $questions = $tradeInService->getQuestions($device->id, $firstNetwork->id);
        $this->assertEmpty($questions->diff($questionsFirstNetwork));
        $this->assertNotEmpty($questions->diff($questionsSecondNetwork));
    }

    /** @test */
    public function should_return_questions_in_correct_order()
    {
        $tradeInService = resolve(TradeInService::class);
        $network        = (new NetworkBuilder())->build();
        $device         = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz           = factory(Quiz::class)->create();
        $questions      = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->generateQuestionTimes(10);
        (new EvaluationBuilder())->withQuiz($quiz)->withNetwork($network)->withDevice($device)->build();

        $questionsAtual    = $tradeInService->getQuestions($device->id, $network->id)->toArray();
        $questionsExpected = [];
        $questions->sortBy('order')->each(function ($question) use (&$questionsExpected) {
            $questionsExpected[] = [
                'id' => $question->id,
                'question' => $question->question,
                'weight' => $question->weight,
                'order' => $question->order,
                'blocker' => $question->blocker ? '1' : '0',
                'description' => $question->description
            ];
        });

        $this->assertEquals($questionsExpected, $questionsAtual);
    }

    /** @test */
    public function should_return_state_defect_device()
    {
        $network  = (new NetworkBuilder())->build();
        $device   = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->withWeight(3)->build();
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);

        $question = [
            [
                'id' => $question->id,
                'answer' => 1
            ]
        ];

        $tradeInService = resolve(TradeInService::class);
        $rating         = $tradeInService->getPrice($device->id, $network->id, $question);

        $this->assertEquals($rating->tierNote, trans('buyback::messages.device_states.defect'));
    }

    /** @test */
    public function should_return_state_good_device()
    {
        $network  = (new NetworkBuilder())->build();
        $device   = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->withWeight(10)->build();
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);

        $question = [
            [
                'id' => $question->id,
                'answer' => 1
            ]
        ];

        $tradeInService = resolve(TradeInService::class);
        $rating         = $tradeInService->getPrice($device->id, $network->id, $question);

        $this->assertEquals($rating->tierNote, trans('buyback::messages.device_states.good'));
    }

    /** @test */
    public function should_return_state_average_device()
    {
        $network  = (new NetworkBuilder())->build();
        $device   = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz     = factory(Quiz::class)->create();
        $question = (new QuestionBuilder())->withNetwork($network)->withQuiz($quiz)->withWeight(7)->build();
        (new EvaluationBuilder())->withDevice($device)->withQuiz($quiz)->withNetwork($network)->build();
        DB::table('deviceTier')->insert(['goodTierNote' => 10, 'middleTierNote' => 7, 'defectTierNote' => 5]);

        $question = [
            [
                'id' => $question->id,
                'answer' => 1
            ]
        ];

        $tradeInService = resolve(TradeInService::class);
        $rating         = $tradeInService->getPrice($device->id, $network->id, $question);

        $this->assertEquals($rating->tierNote, trans('buyback::messages.device_states.average'));
    }
}

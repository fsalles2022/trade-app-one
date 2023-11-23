<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Exceptions\DeviceNotFoundException;
use Buyback\Exceptions\QuizExceptions;
use Buyback\Models\Evaluation;
use Buyback\Models\Quiz;
use Buyback\Services\EvaluationService;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class EvaluationServiceTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_evaluation_service()
    {
        $evaluationService = resolve(EvaluationService::class);
        $className         = get_class($evaluationService);
        $this->assertEquals(EvaluationService::class, $className);
    }

    /** @test */
    public function should_return_a_network_not_found_exception_when_call_create_evaluation_without_a_network()
    {
        $evaluationService = resolve(EvaluationService::class);
        $this->expectException(NetworkNotFoundException::class);
        $evaluationService->createEvaluation(array());
    }

    /** @test */
    public function should_return_a_quiz_not_found_exception_when_call_create_evaluation_without_a_quiz()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $this->expectExceptionMessage(trans('buyback::exceptions.' . QuizExceptions::NOT_FOUND));
        $data = array('network' => $network->slug);
        $evaluationService->createEvaluation($data);
    }

    /** @test */
    public function should_return_a_device_not_found_exception_when_call_create_evaluation_without_a_device()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $quiz              = factory(Quiz::class)->create();
        $this->expectException(DeviceNotFoundException::class);
        $data = array(
            'network' => $network->slug,
            'quizId'  => $quiz->id
        );
        $evaluationService->createEvaluation($data);
    }

    /** @test */
    public function should_return_a_evaluation_instance_when_call_create_evaluation_with_correct_parameters()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $quiz              = factory(Quiz::class)->create();
        $device            = (new DeviceBuilder())->withNetwork($network)->build();
        $data              = array(
            'network'      => $network->slug,
            'quizId'       => $quiz->id,
            'deviceId'     => $device->id,
            'goodValue'    => 600,
            'averageValue' => 400,
            'defectValue'  => 200
        );
        $evaluateReturned  = $evaluationService->createEvaluation($data);
        $this->assertInstanceOf(Evaluation::class, $evaluateReturned);
    }

    /** @test */
    public function should_return_a_network_not_found_exception_when_call_update_evaluation_without_a_network()
    {
        $evaluationService = resolve(EvaluationService::class);
        $evaluation        = (new EvaluationBuilder())->build();
        $this->expectException(NetworkNotFoundException::class);
        $evaluationService->updateEvaluation($evaluation, array());
    }

    /** @test */
    public function should_return_a_quiz_not_found_exception_when_call_update_evaluation_without_a_quiz()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $evaluation        = (new EvaluationBuilder())->withNetwork($network)->build();
        $this->expectExceptionMessage(trans('buyback::exceptions.' . QuizExceptions::NOT_FOUND));
        $data = array('network' => $network->slug);
        $evaluationService->updateEvaluation($evaluation, $data);
    }

    /** @test */
    public function should_return_a_device_not_found_exception_when_call_update_evaluation_without_a_device()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $quiz              = factory(Quiz::class)->create();
        $evaluation        = (new EvaluationBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        $this->expectException(DeviceNotFoundException::class);
        $data = array(
            'network' => $network->slug,
            'quizId'  => $quiz->id
        );
        $evaluationService->updateEvaluation($evaluation, $data);
    }

    /** @test */
    public function should_return_a_evaluation_instance_when_call_update_evaluation_with_correct_parameters()
    {
        $evaluationService = resolve(EvaluationService::class);
        $network           = (new NetworkBuilder())->build();
        $quiz              = factory(Quiz::class)->create();
        $device            = (new DeviceBuilder())->withNetwork($network)->build();
        $evaluation        = (new EvaluationBuilder())->withNetwork($network)->withQuiz($quiz)->build();
        $data              = array(
            'network'  => $network->slug,
            'quizId'   => $quiz->id,
            'deviceId' => $device->id
        );
        $evaluateReturned  = $evaluationService->updateEvaluation($evaluation, $data);
        $this->assertInstanceOf(Evaluation::class, $evaluateReturned);
    }
}

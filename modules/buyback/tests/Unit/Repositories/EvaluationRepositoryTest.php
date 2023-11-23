<?php

namespace Buyback\tests\Unit\Repositories;

use Buyback\Models\Evaluation;
use Buyback\Models\Quiz;
use Buyback\Repositories\EvaluationRepository;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class EvaluationRepositoryTest extends TestCase
{

    private $table = 'evaluations';

    /** @test */
    public function should_return_an_instance_of_evaluation_repository()
    {
        $deviceRepository = new EvaluationRepository();
        $className        = get_class($deviceRepository);
        $this->assertEquals(EvaluationRepository::class, $className);
    }

    /** @test */
    public function should_return_an_evaluation_when_call_find_one_evaluation_by_deviceId()
    {
        $deviceRepository  = new EvaluationRepository();
        $evaluationEntity  = (new EvaluationBuilder())->build();
        $devicesCollection = $deviceRepository->findOneEvaluationByDeviceNetworkId($evaluationEntity->id);
        $className         = get_class($devicesCollection);
        $this->assertEquals(get_class(new Evaluation()), $className);
    }

    /** @test */
    public function should_return_and_create_an_evaluation_when_call_create_evaluation_with_correct_parameters()
    {
        $deviceRepository   = new EvaluationRepository();
        $network            = (new NetworkBuilder())->build();
        $device             = (new DeviceBuilder())->withNetwork($network)->build();
        $quiz               = factory(Quiz::class)->create();
        $devicePivotNetwork = $device->networks()->withPivot('id')->get(['devices_network.id'])->toArray();

        $quizId               = $quiz->id;
        $devicePivotNetworkId = data_get($devicePivotNetwork, '0.pivot.id');
        $data                 = [
            'quizId'          => $quizId,
            'deviceNetworkId' => $devicePivotNetworkId,
            'goodValue'       => 600,
            'averageValue'    => 400,
            'defectValue'     => 200
        ];
        $evaluateReturned     = $deviceRepository->createEvaluation($data, $quiz);
        $this->assertInstanceOf(Evaluation::class, $evaluateReturned);
        $this->assertDatabaseHas($this->table, $data);
    }

    /** @test */
    public function should_return_an_model_invalid_exception_when_call_create_evaluation_with_incorrect_parameters()
    {
        $deviceRepository = new EvaluationRepository();
        $quiz             = factory(Quiz::class)->create();
        $incorrectPayload = [];
        $this->expectException(ModelInvalidException::class);
        $deviceRepository->createEvaluation($incorrectPayload, $quiz);
    }

    /** @test */
    public function should_return_and_update_an_evaluation_when_call_update_evaluation_with_correct_parameters()
    {
        $deviceRepository = new EvaluationRepository();
        $evaluation       = (new EvaluationBuilder())->build();
        $quiz             = factory(Quiz::class)->create();

        $data             = [
            'quizId' => $quiz->id
        ];
        $evaluateReturned = $deviceRepository->updateEvaluation($evaluation, $data);
        $this->assertInstanceOf(Evaluation::class, $evaluateReturned);
        $this->assertDatabaseHas($this->table, $data);
    }
}

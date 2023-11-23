<?php

namespace Buyback\Tests\Unit\Importables;

use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Models\Evaluation;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use TradeAppOne\Domain\Importables\EvaluationImportable;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotAssociatedWithUserException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class EvaluationImportableTest extends TestCase
{

    protected $tableName = 'evaluations';

    /** @test */
    public function should_return_evaluation_instance_when_completed()
    {

        $network    = (new NetworkBuilder())->build();
        $evaluation = (new EvaluationBuilder())->withNetwork($network)->build();
        $user       = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationImportable = resolve(EvaluationImportable::class);
        $line                 = $this->getLine($network->slug, $evaluation);
        $evaluationCreated    = $evaluationImportable->processLine($line);

        $className = get_class($evaluationCreated);
        $this->assertEquals(Evaluation::class, $className);
    }

    /** @test */
    public function should_create_a_new_evaluation_if_there_is_no()
    {
        $network    = (new NetworkBuilder())->build();
        $evaluation = (new EvaluationBuilder())->withNetwork($network)->build();
        $user       = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationImportable = resolve(EvaluationImportable::class);
        $line                 = $this->getLine($network->slug, $evaluation);
        $evaluationCreated    = $evaluationImportable->processLine($line);
        $this->assertDatabaseHas($this->tableName, ['id' => $evaluationCreated->id]);
    }

    /** @test */
    public function should_update_a_evaluation_if_it_exists()
    {
        $network           = (new NetworkBuilder())->build();
        $outdateEvaluation = (new EvaluationBuilder())->withNetwork($network)->build();
        $dataCreated       = [
            'goodValue'    => $outdateEvaluation->goodValue,
            'averageValue' => $outdateEvaluation->averageValue,
            'defectValue'  => $outdateEvaluation->defectValue
        ];
        $user              = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $this->assertDatabaseHas($this->tableName, $dataCreated);

        $evaluationImportable = resolve(EvaluationImportable::class);
        $line                 = $this->getLine($network->slug, $outdateEvaluation);
        $line['goodValue']    = 500;
        $line['averageValue'] = 300;
        $line['defectValue']  = 100;
        $evaluationImportable->processLine($line);
        $dataExpect = [
            'goodValue'    => $line['goodValue'],
            'averageValue' => $line['averageValue'],
            'defectValue'  => $line['defectValue']
        ];
        $this->assertDatabaseHas($this->tableName, $dataExpect);
    }

    /** @test */
    public function should_return_device_not_sould_by_network_exception_when_device_not_associate_a_network()
    {
        $network    = (new NetworkBuilder())->build();
        $evaluation = (new EvaluationBuilder())->build();
        $user       = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationImportable = resolve(EvaluationImportable::class);
        $line                 = $this->getLine($network->slug, $evaluation);
        $this->expectException(DeviceNotSouldByNetworkException::class);
        $evaluationImportable->processLine($line);
    }

    /** @test */
    public function should_return_network_not_associated_with_user_exception_when_user_not_associate_a_network()
    {
        $network    = (new NetworkBuilder())->build();
        $evaluation = (new EvaluationBuilder())->build();
        $user       = (new UserBuilder())->build();
        $this->be($user);

        $evaluationImportable = resolve(EvaluationImportable::class);
        $line                 = $this->getLine($network->slug, $evaluation);
        $this->expectException(NetworkNotAssociatedWithUserException::class);
        $evaluationImportable->processLine($line);
    }

    private function getLine(string $networkSlug, Evaluation $evaluation)
    {
        $evaluationImportable = resolve(EvaluationImportable::class);

        $columns = array_keys($evaluationImportable->getColumns());
        $lines   = $evaluationImportable->getExample($networkSlug, $evaluation);
        return array_combine($columns, $lines);
    }
}

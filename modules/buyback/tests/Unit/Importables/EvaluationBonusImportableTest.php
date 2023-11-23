<?php


namespace Buyback\Tests\Unit\Importables;

use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Models\EvaluationsBonus;
use Buyback\Tests\Helpers\Builders\EvaluationBonusBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use TradeAppOne\Domain\Importables\EvaluationBonusImportable;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotAssociatedWithUserException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class EvaluationBonusImportableTest extends TestCase
{

    protected $tableName = 'evaluations_bonus';

    /** @test */
    public function should_return_evaluation_bonus_instance_when_completed(): void
    {

        $network    = (new NetworkBuilder())->build();
        $evaluation = (new EvaluationBuilder())->withNetwork($network)->build();

        $evaluationBonus = (new EvaluationBonusBuilder())->withEvaluation($evaluation)->build();
        $user            = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);
        $line                      = $this->getLine($network->slug, $evaluationBonus);
        $evaluationBonusCreated    = $evaluationBonusImportable->processLine($line);

        $className = get_class($evaluationBonusCreated);
        $this->assertEquals(EvaluationsBonus::class, $className);
    }

    /** @test */
    public function should_create_a_new_evaluation_bonus_if_there_is_no(): void
    {
        $network         = (new NetworkBuilder())->build();
        $evaluation      = (new EvaluationBuilder())->withNetwork($network)->build();
        $evaluationBonus = (new EvaluationBonusBuilder())->withEvaluation($evaluation)->build();

        $user = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);
        $line                      = $this->getLine($network->slug, $evaluationBonus);
        $evaluationBonusCreated    = $evaluationBonusImportable->processLine($line);
        $this->assertDatabaseHas($this->tableName, ['id' => $evaluationBonusCreated->id]);
    }

    /** @test */
    public function should_update_a_evaluation_bonus_if_it_exists(): void
    {
        $network                 = (new NetworkBuilder())->build();
        $evaluation              = (new EvaluationBuilder())->withNetwork($network)->build();
        $evaluationBonusOutdated = (new EvaluationBonusBuilder())->withEvaluation($evaluation)->build();
        $dataCreated             = [
            'evaluationId'  => data_get($evaluationBonusOutdated->evaluation, 'id', 0),
            'goodValue'    => $evaluationBonusOutdated->goodValue,
            'averageValue'    => $evaluationBonusOutdated->averageValue,
            'defectValue'    => $evaluationBonusOutdated->defectValue,
            'sponsor'       => $evaluationBonusOutdated->sponsor
        ];
        $user                    = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $this->assertDatabaseHas($this->tableName, $dataCreated);

        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);
        $line                      = $this->getLine($network->slug, $evaluationBonusOutdated);
        $line['evaluationId']      = data_get($dataCreated, 'evaluationId');
        $line['goodValue']         = 100.00;
        $line['averageValue']      = 50.00;
        $line['defectValue']       = 30.00;
        $line['sponsor']           = data_get($dataCreated, 'sponsor');
        $evaluationBonusImportable->processLine($line);
        $dataExpect = [
            'evaluationId'  => $line['evaluationId'],
            'goodValue'     => $line['goodValue'],
            'averageValue'  => $line['averageValue'],
            'defectValue'   => $line['defectValue'],
            'sponsor'       => $line['sponsor']
        ];
        $this->assertDatabaseHas($this->tableName, $dataExpect);
    }

    /** @test */
    public function should_return_device_not_sould_by_network_exception_when_device_not_associate_a_network(): void
    {
        $network         = (new NetworkBuilder())->build();
        $evaluationBonus = (new EvaluationBonusBuilder())->build();

        $user = (new UserBuilder())->withNetwork($network)->build();
        $this->be($user);

        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);
        $line                      = $this->getLine($network->slug, $evaluationBonus);
        $this->expectException(DeviceNotSouldByNetworkException::class);
        $evaluationBonusImportable->processLine($line);
    }

    /** @test */
    public function should_return_network_not_associated_with_user_exception_when_user_not_associate_a_network(): void
    {
        $network         = (new NetworkBuilder())->build();
        $evaluation      = (new EvaluationBuilder())->build();
        $evaluationBonus = (new EvaluationBonusBuilder())->withEvaluation($evaluation)->build();
        $user            = (new UserBuilder())->build();
        $this->be($user);

        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);
        $line                      = $this->getLine($network->slug, $evaluationBonus);
        $this->expectException(NetworkNotAssociatedWithUserException::class);
        $evaluationBonusImportable->processLine($line);
    }

    private function getLine(string $networkSlug, EvaluationsBonus $evaluationBonus)
    {
        $evaluationBonusImportable = resolve(EvaluationBonusImportable::class);

        $columns = array_keys($evaluationBonusImportable->getColumns());
        $lines   = $evaluationBonusImportable->getExample($networkSlug, $evaluationBonus);
        return array_combine($columns, $lines);
    }
}

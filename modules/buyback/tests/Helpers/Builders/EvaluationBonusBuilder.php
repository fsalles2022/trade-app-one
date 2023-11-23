<?php


namespace Buyback\Tests\Helpers\Builders;

use Buyback\Models\EvaluationsBonus;
use Buyback\Models\Evaluation;

class EvaluationBonusBuilder
{

    protected $evaluation;

    public function withEvaluation(Evaluation $evaluation): EvaluationBonusBuilder
    {
        $this->evaluation = $evaluation;
        return $this;
    }

    public function build(): EvaluationsBonus
    {
        $evaluationEntity = $this->evaluation ?? (new EvaluationBuilder())->build();
        return factory(EvaluationsBonus::class)->create([
            'evaluationId' => $evaluationEntity->id
        ]);
    }
}

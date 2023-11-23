<?php


namespace Buyback\Repositories;

use Buyback\Models\EvaluationsBonus;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class EvaluationBonusRepository extends BaseRepository
{
    protected $model = EvaluationsBonus::class;

    public function createBonus(array $data)
    {
        $evaluationBonus = new $this->model;
        $evaluationBonus->fill($data)->validate();
        $evaluationBonus->save();

        return $evaluationBonus;
    }

    public function updateBonus(EvaluationsBonus $evaluationBonus, array $data): EvaluationsBonus
    {
        $evaluationBonus->fill($data);
        $evaluationBonus->restore();
        $evaluationBonus->save();

        return $evaluationBonus;
    }

    public function bonusByEvaluationIdAndSponsor(int $evaluationId, string $sponsor): ?EvaluationsBonus
    {
        return EvaluationsBonus::where('evaluationId', $evaluationId)
            ->where('sponsor', 'like', '%'.$sponsor.'%')
            ->first();
    }

    public function bonusByEvaluationId(int $evaluationId): Collection
    {
        return EvaluationsBonus::where('evaluationId', $evaluationId)->get();
    }
}

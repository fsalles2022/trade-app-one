<?php


namespace Buyback\Services;

use Buyback\Exceptions\EvaluationNotFoundException;
use Buyback\Models\EvaluationsBonus;
use Buyback\Repositories\EvaluationBonusRepository;
use Buyback\Repositories\EvaluationRepository;

class EvaluationBonusService
{
    private $evaluationRepository;
    private $evaluationBonusRepository;

    public function __construct(
        EvaluationRepository $evaluationRepository,
        EvaluationBonusRepository $evaluationBonusRepository
    ) {
        $this->evaluationRepository      = $evaluationRepository;
        $this->evaluationBonusRepository = $evaluationBonusRepository;
    }

    public function createBonus(array $data): ?EvaluationsBonus
    {
        $validate = $this->checksIfTheParametersExist($data);
        if ($validate) {
            return $this->evaluationBonusRepository->createBonus($data);
        }
        return null;
    }

    public function updateBonus(EvaluationsBonus $evaluationBonus, array $data): ?EvaluationsBonus
    {
        $validate = $this->checksIfTheParametersExist($data);
        if ($validate) {
            return $this->evaluationBonusRepository->updateBonus($evaluationBonus, $data);
        }
        return null;
    }

    /** @throws */
    private function checksIfTheParametersExist(array $data): bool
    {
        $evaluation = $this->evaluationRepository->find(data_get($data, 'evaluationId'));
        throw_if(($evaluation === null), EvaluationNotFoundException::class);

        return array_key_exists('goodValue', $data) &&
            array_key_exists('averageValue', $data) &&
            array_key_exists('defectValue', $data) &&
            array_key_exists('sponsor', $data) &&
            array_key_exists('evaluationId', $data);
    }

    public function bonusByEvaluationIdAndSponsor(int $evaluationId = 0, string $sponsor = ''): ?EvaluationsBonus
    {
        return $this->evaluationBonusRepository->bonusByEvaluationIdAndSponsor($evaluationId, $sponsor);
    }
}

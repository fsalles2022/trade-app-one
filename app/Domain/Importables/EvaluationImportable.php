<?php

namespace TradeAppOne\Domain\Importables;

use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Models\Evaluation;
use Buyback\Services\DeviceService;
use Buyback\Services\EvaluationService;
use Buyback\Services\QuizService;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotAssociatedWithUserException;

class EvaluationImportable implements ImportableInterface
{
    private $userService;
    private $networkService;
    private $hierarchyService;
    private $deviceService;
    private $quizService;
    private $evaluationService;

    public function __construct(
        UserService $userService,
        NetworkService $networkService,
        HierarchyService $hierarchyService,
        DeviceService $deviceService,
        QuizService $quizService,
        EvaluationService $evaluationService
    ) {
        $this->userService       = $userService;
        $this->networkService    = $networkService;
        $this->hierarchyService  = $hierarchyService;
        $this->deviceService     = $deviceService;
        $this->quizService       = $quizService;
        $this->evaluationService = $evaluationService;
    }


    public function getExample(string $networkSlug = null, Evaluation $evaluation = null)
    {
        return [
            $networkSlug ?? 'Rede Exemplo',
            $evaluation->quizId ?? '1',
            $evaluation->deviceNetworkId ?? '1',
            $evaluation->goodValue ?? '800',
            $evaluation->averageValue ?? '600',
            $evaluation->defectValue ?? '300'
        ];
    }

    public function getColumns()
    {
        return [
            'network'      => trans('importables.network'),
            'quizId'       => trans('importables.buyback.evaluation.quizId'),
            'deviceId'     => trans('importables.buyback.evaluation.deviceId'),
            'goodValue'    => trans('importables.buyback.evaluation.goodValue'),
            'averageValue' => trans('importables.buyback.evaluation.averageValue'),
            'defectValue'  => trans('importables.buyback.evaluation.defectValue')
        ];
    }

    /** @throws */
    public function processLine($line)
    {
        $network = $this->networkService->findOneBySlug($line['network']);
        $this->checkIfLoggedUserHasNetwork($network->id);
        $deviceNetwork = $this->deviceService->deviceByDeviceIdAndNetworkId($line['deviceId'], $network->id);
        throw_if((is_null($deviceNetwork)), new DeviceNotSouldByNetworkException());
        $evaluation = $this->evaluationService->evaluationByDeviceNetworkId($deviceNetwork['id']);
        if ($evaluation instanceof Evaluation) {
            $evaluation = $this->evaluationService->updateEvaluation($evaluation, $line);
        } else {
            $evaluation = $this->evaluationService->createEvaluation($line);
        }

        return $evaluation;
    }

    /** @throws */
    private function checkIfLoggedUserHasNetwork(int $networkId)
    {
        $loggedUser         = $this->userService->getAuthenticatedUser();
        $networksCollection = $this->hierarchyService->getNetworksThatBelongsToUser($loggedUser);
        $networksId         = $networksCollection->pluck('id')->toArray();
        if (! in_array($networkId, $networksId)) {
            throw new NetworkNotAssociatedWithUserException();
        }
    }

    public function getType()
    {
        return Importables::EVALUATIONS;
    }
}

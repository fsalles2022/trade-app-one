<?php

namespace Buyback\Services;

use Buyback\Exceptions\DeviceNotFoundException;
use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Exceptions\QuizExceptions;
use Buyback\Models\Evaluation;
use Buyback\Repositories\EvaluationRepository;
use TradeAppOne\Domain\Exportables\EvaluationExportable;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Domain\Services\NetworkService;

class EvaluationService
{
    private $evaluationRepository;
    private $networkService;
    private $deviceService;
    private $quizService;
    private $authorizations;

    public function __construct(
        NetworkService $networkService,
        DeviceService $deviceService,
        QuizService $quizService,
        EvaluationRepository $evaluationRepository,
        Authorizations $authorizations
    ) {
        $this->evaluationRepository = $evaluationRepository;
        $this->networkService       = $networkService;
        $this->deviceService        = $deviceService;
        $this->quizService          = $quizService;
        $this->authorizations       = $authorizations;
    }

    public function evaluationByDeviceNetworkId(int $deviceNetworkId): ?Evaluation
    {
        return $this->evaluationRepository->findOneEvaluationByDeviceNetworkId($deviceNetworkId);
    }

    public function storeEvaluation(array $attributes)
    {
        return $this->evaluationRepository->create($attributes);
    }

    public function createEvaluation(array $data): Evaluation
    {
        $validate                = $this->checksIfTheParametersExist($data);
        $devicePivotNetwork      = $this->checkIfTheDeviceIsMarketedByNetwork($validate);
        $deviceNetworkId         = data_get($devicePivotNetwork, 'id');
        $data['deviceNetworkId'] = $deviceNetworkId;

        $evaluation = $this->evaluationRepository->createEvaluation($data, $validate['quiz']);
        return $evaluation;
    }

    public function updateEvaluation(Evaluation $evaluation, array $data): Evaluation
    {
        $validate = $this->checksIfTheParametersExist($data);
        $this->checkIfTheDeviceIsMarketedByNetwork($validate);

        return $this->evaluationRepository->updateEvaluation($evaluation, $data);
    }

    /** @throws */
    private function checksIfTheParametersExist(array $data)
    {
        $network = $this->networkService->findOneBySlug(data_get($data, 'network'));
        $quiz    = $this->quizService->quizById(data_get($data, 'quizId'));
        throw_if((is_null($quiz)), QuizExceptions::notFound());
        $device = $this->deviceService->deviceById(data_get($data, 'deviceId'));
        throw_if((is_null($device)), new DeviceNotFoundException());
        return [
            'network' => $network,
            'quiz'    => $quiz,
            'device'  => $device
        ];
    }

    /** @throws */
    private function checkIfTheDeviceIsMarketedByNetwork(array $validate)
    {
        $network = data_get($validate, 'network');
        $device  = data_get($validate, 'device');

        $device = $this->deviceService->deviceByDeviceIdAndNetworkId($device->id, $network->id);
        throw_if((is_null($device)), new DeviceNotSouldByNetworkException());
        return $device;
    }

    public function devicesEvaluationsPaginated(array $filters)
    {
        $networks    = $this->authorizations->getNetworksAuthorized()->pluck('id');
        $evaluations = $this->evaluationRepository->getDevicesEvaluationsAndFilter($filters, $networks);

        return $evaluations->paginate(10);
    }

    public function export(array $filters)
    {
        $networks =  $this->authorizations->getNetworksAuthorized()->pluck('id');
        $devices  =  $this->evaluationRepository->getDevicesEvaluationsAndFilter($filters, $networks)->get();

        return (new EvaluationExportable($devices));
    }
}

<?php


namespace TradeAppOne\Domain\Importables;

use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Models\EvaluationsBonus;
use Buyback\Services\DeviceService;
use Buyback\Services\EvaluationBonusService;
use Buyback\Services\EvaluationService;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotAssociatedWithUserException;

class EvaluationBonusImportable implements ImportableInterface
{
    private $userService;
    private $networkService;
    private $hierarchyService;
    private $deviceService;
    private $evaluationBonusService;
    private $evaluationService;

    public function __construct(
        UserService $userService,
        NetworkService $networkService,
        HierarchyService $hierarchyService,
        DeviceService $deviceService,
        EvaluationBonusService $evaluationBonusService,
        EvaluationService $evaluationService
    ) {
        $this->userService      = $userService;
        $this->networkService   = $networkService;
        $this->hierarchyService = $hierarchyService;
        $this->deviceService    = $deviceService;

        $this->evaluationService      = $evaluationService;
        $this->evaluationBonusService = $evaluationBonusService;
    }


    public function getExample(string $networkSlug = null, EvaluationsBonus $evaluationBonus = null): array
    {
        return [
            $networkSlug ?? 'Rede Exemplo',
            '1',
            '1',
            $evaluationBonus->goodValue ?? 800.00,
            $evaluationBonus->averageValue ?? 600.00,
            $evaluationBonus->defectValue ?? 300.00,
            $evaluationBonus->sponsor ?? 'Nome do Patrocinador'
        ];
    }

    public function getColumns(): array
    {
        return [
            'network'       => trans('importables.network'),
            'quizId'        => trans('importables.buyback.evaluation.quizId'),
            'deviceId'      => trans('importables.buyback.evaluation.deviceId'),
            'goodValue'     => trans('importables.buyback.evaluation.goodValue'),
            'averageValue'  => trans('importables.buyback.evaluation.averageValue'),
            'defectValue'   => trans('importables.buyback.evaluation.defectValue'),
            'sponsor'       => trans('importables.buyback.evaluation.sponsor')
        ];
    }

    /** @throws */
    public function processLine($line): ?EvaluationsBonus
    {
        $network   = $this->networkService->findOneBySlug(data_get($line, 'network'));
        $networkId = data_get($network, 'id', 0);
        $this->checkIfLoggedUserHasNetwork($networkId);

        $deviceNetwork = $this->deviceService->deviceByDeviceIdAndNetworkId(data_get($line, 'deviceId'), $networkId);
        throw_if(($deviceNetwork === null), new DeviceNotSouldByNetworkException());

        $evaluation   = $this->evaluationService->evaluationByDeviceNetworkId($deviceNetwork['id']);
        $evaluationId = data_get($evaluation, 'id', 0);

        $evaluationBonus = $this->evaluationBonusService->bonusByEvaluationIdAndSponsor($evaluationId, data_get($line, 'sponsor'));
        data_set($line, 'evaluationId', $evaluationId);

        if ($evaluationBonus instanceof EvaluationsBonus) {
            $bonus = $this->evaluationBonusService->updateBonus($evaluationBonus, $line);
        } else {
            $bonus = $this->evaluationBonusService->createBonus($line);
        }
        return $bonus;
    }

    /** @throws */
    private function checkIfLoggedUserHasNetwork(int $networkId): void
    {
        $loggedUser         = $this->userService->getAuthenticatedUser();
        $networksCollection = $this->hierarchyService->getNetworksThatBelongsToUser($loggedUser);
        $networksId         = $networksCollection->pluck('id')->toArray();
        if (! in_array($networkId, $networksId, true)) {
            throw new NetworkNotAssociatedWithUserException();
        }
    }

    public function getType(): string
    {
        return Importables::EVALUATIONS_BONUS;
    }
}

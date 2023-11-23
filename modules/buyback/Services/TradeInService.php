<?php

namespace Buyback\Services;

use Buyback\Exceptions\DeviceNotFoundException;
use Buyback\Exceptions\DeviceNotSouldByNetworkException;
use Buyback\Exceptions\EvaluationNotFoundException;
use Buyback\Exceptions\NumberOfQuestionsOtherThanAnswersException;
use Buyback\Exceptions\QuestionsNotFoundException;
use Buyback\Exceptions\TradeInExceptions;
use Buyback\Helpers\MergeQuestionsWithAnswers;
use Buyback\Helpers\SumQuestionsWeight;
use Buyback\Models\EvaluationsBonus;
use Buyback\Repositories\EvaluationBonusRepository;
use Buyback\Models\DevicesNetwork;
use Buyback\Models\Evaluation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SaleService;
use Illuminate\Database\Eloquent\Collection as CollectionAlias;

class TradeInService
{
    /** @var DeviceService */
    protected $deviceService;

    /** @var EvaluationService */
    protected $evaluationService;

    /** @var OfferDeclinedService */
    protected $offerDeclinedService;

    /** @var EvaluationBonusRepository */
    protected $evaluationBonusRepository;

    public function __construct(
        DeviceService $deviceService,
        EvaluationService $evaluationService,
        OfferDeclinedService $offerDeclinedService,
        SaleService $saleService,
        EvaluationBonusRepository $evaluationBonusRepository
    ) {
        $this->deviceService             = $deviceService;
        $this->evaluationService         = $evaluationService;
        $this->offerDeclinedService      = $offerDeclinedService;
        $this->saleService               = $saleService;
        $this->evaluationBonusRepository = $evaluationBonusRepository;
    }

    public function mountEvaluationForm(DevicesNetwork $deviceNetwork, int $networkId, array $questionsAnswer): EvaluationProducerFromQuestions
    {
        $evaluation  = $this->getEvaluation($deviceNetwork->id);
        $weightSum   = $this->fetchQuestionsWithWeight($deviceNetwork->device->id, $networkId, $questionsAnswer);
        $devicePrice = $this->ratingBasedOnWeight($evaluation->toArray(), $weightSum)->price;

        $questions = $this->getQuestions($deviceNetwork->device->id, $networkId)->toArray();

        return (new EvaluationProducerFromQuestions([
            'devicePrice' => $devicePrice,
            'deviceNote' => $weightSum
        ], MergeQuestionsWithAnswers::merge($questions, $questionsAnswer)));
    }

    public function mountEvaluationFromService(Service $service, array $questionsAnswer): EvaluationProducerFromQuestions
    {
        $sale      = $service->sale;
        $deviceId  = data_get($service, 'device.id');
        $networkId = data_get($sale, 'pointOfSale.network.id');
        $weightSum = $this->fetchQuestionsWithWeight($deviceId, $networkId, $questionsAnswer, $service);

        $evaluationValues = data_get($service, 'evaluationsValues');

        if ($evaluationValues === null) {
            $evaluationValues = $this->updateServiceWhenNotExistsEvaluationsValues($service, $deviceId, $networkId);
        }

        return (new EvaluationProducerFromQuestions([
            'devicePrice' => $this->ratingBasedOnWeight($evaluationValues, $weightSum)->price,
            'deviceNote' => $weightSum
        ], $questionsAnswer));
    }

    public function getPrice($deviceId, $networkId, $questionsAnswer): DeviceRated
    {
        $deviceNetwork = $this->deviceService->deviceByDeviceIdAndNetworkId($deviceId, $networkId);
        throw_if(($deviceNetwork === null), new DeviceNotSouldByNetworkException());

        $evaluation = $this->getEvaluation(data_get($deviceNetwork, 'id'));

        $weightSum = $this->fetchQuestionsWithWeight($deviceId, $networkId, $questionsAnswer);

        return $this->ratingBasedOnWeight($evaluation->toArray(), $weightSum);
    }

    public function getBonusPrice(int $deviceId, int $networkId, ?DeviceRated $rating = null): Collection
    {
        $deviceNetwork = $this->deviceService->deviceByDeviceIdAndNetworkId($deviceId, $networkId);
        throw_if(($deviceNetwork === null), new DeviceNotSouldByNetworkException());

        $evaluation = $this->getEvaluation(data_get($deviceNetwork, 'id'));
        if ($evaluation) {
            $bonus = $this->evaluationBonusRepository->bonusByEvaluationId($evaluation->id);
            return $this->selectBonusByRating($bonus, $rating);
        }
        return collect([]);
    }

    public function getDevicesWithFilters(array $networksId, array $filters): Collection
    {
        return $this->deviceService->devicesByNetworksIdWithFilters($networksId, $filters);
    }

    /** @throws */
    public function registerOfferDeclined(User $user, array $parameters): Model
    {
        $device  = $this->deviceService->findDeviceById(data_get($parameters, 'device.id'));
        $network = $user->getNetwork();

        $deviceAssociatedWithTheNetwork = $device->networks()->where('networkId', $network->id)->get();

        if ($deviceAssociatedWithTheNetwork->isEmpty()) {
            throw new DeviceNotFoundException();
        }

        $deviceImei      = data_get($parameters, 'device.imei');
        $questionsAnswer = data_get($parameters, 'questions');
        $customer        = data_get($parameters, 'customer');
        $reason          = data_get($parameters, 'reason');
        $operator        = data_get($parameters, 'operator');
        $operation       = data_get($parameters, 'operation');

        $weightSum = $this->fetchQuestionsWithWeight($device->id, $network->id, $questionsAnswer);
        $questions = $this->getQuestions($device->id, $network->id);

        $deviceNetwork = $this->deviceService->deviceByDeviceIdAndNetworkId($device->id, $network->id);
        throw_if(($device === null), new DeviceNotSouldByNetworkException());

        $evaluation  = $this->getEvaluation(data_get($deviceNetwork, 'id'));
        $devicePrice = $this->ratingBasedOnWeight($evaluation->toArray(), $weightSum)->price;

        $questionsMerged = MergeQuestionsWithAnswers::merge($questions->toArray(), $questionsAnswer);

        $offerDeclinedPayload = (new MountOfferDeclinedPayload)
            ->addOperator($operator)
            ->addOperation($operation)
            ->addCustomer($customer)
            ->addReason($reason)
            ->addUser($user)
            ->addDevice($device)
            ->addImei($deviceImei)
            ->addPrice($devicePrice)
            ->addWeight($weightSum)
            ->addQuestions($questionsMerged)
            ->mount();

        return $this->offerDeclinedService->new($offerDeclinedPayload);
    }

    /** @throws */
    public function fetchQuestionsWithWeight(int $deviceId, int $networkId, array $questionsAnswer, $service = null): int
    {
        if ($service) {
            $questions = collect(data_get($service, 'evaluations.salesman.questions'));
        } else {
            $questions = $this->getQuestions($deviceId, $networkId);
        }

        $questionsDifferInSize = ($questions->count() != count($questionsAnswer));

        if ($questionsDifferInSize) {
            throw new NumberOfQuestionsOtherThanAnswersException();
        }

        return SumQuestionsWeight::getWeightSum($questions, $questionsAnswer);
    }

    /** @throws */
    public function getQuestions(int $deviceId, int $networkId): Collection
    {
        $device = $this->deviceService->deviceByDeviceIdAndNetworkId($deviceId, $networkId);
        throw_if(($device === null), new DeviceNotSouldByNetworkException());

        $questions = $this
            ->getEvaluation(data_get($device, 'id'))
            ->quiz()
            ->first()
            ->questions()
            ->get();

        $questionsOrdered = $questions->sortBy('order')->values();

        if ($questions->isEmpty()) {
            throw new QuestionsNotFoundException();
        }

        return $questionsOrdered;
    }

    /** @throws */
    public function getEvaluation(int $deviceNetworkId): Evaluation
    {
        $evaluation = $this->evaluationService->evaluationByDeviceNetworkId($deviceNetworkId);

        if ($evaluation === null) {
            throw new EvaluationNotFoundException();
        }

        return $evaluation;
    }

    public function ratingBasedOnWeight(array $evaluation, $weightSum): DeviceRated
    {
        $tierNotes      = $this->deviceService->deviceTierNotes();
        $middleTierNote = data_get($tierNotes, 'middleTierNote');
        $defectTierNote = data_get($tierNotes, 'defectTierNote');

        if ($weightSum <= $defectTierNote) {
            $devicePrice = data_get($evaluation, 'defectValue');
            $state       = trans('buyback::messages.device_states.defect');
        } elseif ($weightSum <= $middleTierNote) {
            $devicePrice = data_get($evaluation, 'averageValue');
            $state       = trans('buyback::messages.device_states.average');
        } else {
            $devicePrice = data_get($evaluation, 'goodValue');
            $state       = trans('buyback::messages.device_states.good');
        }
        return new DeviceRated($devicePrice, $weightSum, $state);
    }

    private function updateServiceWhenNotExistsEvaluationsValues($service, $deviceId, $networkId): array
    {
        $deviceNetwork   = $this->deviceService->deviceByDeviceIdAndNetworkId($deviceId, $networkId);
        $deviceNetworkId = data_get($deviceNetwork, 'id');

        $evaluation = $this->getEvaluation($deviceNetworkId);

        $service = $this->saleService->updateService($service, [
            'evaluationsValues' => $evaluation->toMongoAggregation()
        ]);

        return $service['evaluationsValues'];
    }

    public function findWatch(array $requestData): Device
    {
        $serialNumber = data_get($requestData, 'serialNumber', '');
        if (! empty($serialNumber)) {
            $lastFourDigits = substr($serialNumber, strlen($serialNumber) - 4, strlen($serialNumber));
            $devicesNetwork = $this->deviceService->findDeviceByPartialSku($lastFourDigits);

            throw_if($devicesNetwork->isEmpty(), new DeviceNotSouldByNetworkException());

            $filteredWatch = $devicesNetwork->filter(static function ($deviceNetwork) {
                return data_get($deviceNetwork->device, 'type', '') === Device::SMARTWATCH_TYPE;
            });

            return $filteredWatch->first()->device;
        }
        throw TradeInExceptions::deviceNotBelongToNetwork();
    }

    /**
     * @param mixed[] $requestData
     * @return Collection
     * @throws \Throwable
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    public function findIpad(array $requestData): Collection
    {
        $serialNumber = data_get($requestData, 'serialNumber', '');
        if (! empty($serialNumber)) {
            $devicesNetwork = $this->deviceService->findDeviceBySku($serialNumber);

            throw_if($devicesNetwork->isEmpty(), new DeviceNotSouldByNetworkException());

            return $this->deviceService
                ->getAllIpads()
                ->with('device')
                ->get();
        }
        throw TradeInExceptions::deviceNotBelongToNetwork();
    }

    private function selectBonusByRating(Collection $bonusItems, ?DeviceRated $rating): Collection
    {
        if (($rating instanceof DeviceRated) && $bonusItems->isNotEmpty()) {
            $bonusItems->transform(function (EvaluationsBonus $bonus) use (&$rating) {
                $deviceRatedBonus  = $this->ratingBasedOnWeight($bonus->toArray(), data_get($rating, 'note'));
                $bonus->bonusValue = data_get($deviceRatedBonus, 'price', 0.00);
                return $bonus;
            });
            return $bonusItems;
        }
        return collect([]);
    }

    public function getVoucherExpires(): CollectionAlias
    {
        return $this->saleService->filterAll([
            'operator' => Operations::TRADE_IN_MOBILE,
            'status' => ServiceStatus::ACCEPTED
        ]);
    }
}

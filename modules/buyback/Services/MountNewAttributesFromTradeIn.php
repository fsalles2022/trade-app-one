<?php

namespace Buyback\Services;

use Buyback\Exceptions\TradeInExceptions;
use Buyback\Models\DevicesNetwork;
use Illuminate\Support\Facades\Validator;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Exceptions\BusinessExceptions\ModelInvalidException;
use Buyback\Models\EvaluationsBonus;
use Illuminate\Support\Collection;

class MountNewAttributesFromTradeIn implements MountNewAttributesService
{
    protected $deviceService;
    protected $tradeInService;

    public function __construct(
        DeviceService $deviceService,
        TradeInService $tradeInService
    ) {
        $this->deviceService  = $deviceService;
        $this->tradeInService = $tradeInService;
    }

    public function getAttributes(array $service): array
    {
        $this->validateService($service);

        $deviceId        = $service['deviceId'];
        $networkId       = $service['networkId'];
        $questionsAnswer = $service['questions'];

        $deviceNetwork = $this->getDevicesNetwork($deviceId, $networkId);

        if (is_null($deviceNetwork)) {
            throw TradeInExceptions::deviceNotBelongToNetwork();
        }

        $deviceEvaluation = $this
            ->tradeInService
            ->mountEvaluationForm($deviceNetwork, $networkId, $questionsAnswer);

        $evaluation = $this->tradeInService->getEvaluation($deviceNetwork->id);

        $deviceAggregation = $deviceNetwork->toMongoAggregation($service['imei']);

        $tradeInServiceStructure = [
            'device' => $deviceAggregation,
            'label' => $deviceAggregation['label'],
            'price' => $deviceEvaluation->price,
            'evaluationsValues' => $evaluation->toMongoAggregation(),
            'evaluations' => [
                'salesman' => $deviceEvaluation->toArray()
            ],
        ];

        $bonusFromRequest    = data_get($service, 'device.price.bonus', []);
        $bonusIdsFromRequest = collect($bonusFromRequest)->pluck('id');

        $rating          = data_get($service, 'device.price', []);
        $deviceRated     = new DeviceRated(
            data_get($rating, 'price'),
            data_get($rating, 'note'),
            data_get($rating, 'tierNote')
        );
        $evaluationBonus = $this->tradeInService->getBonusPrice($deviceId, $networkId, $deviceRated);
        if ($evaluationBonus->isNotEmpty() && $bonusIdsFromRequest->isNotEmpty()) {
            $filteredBonus              = $evaluationBonus->filter(static function (EvaluationsBonus $bonus) use ($bonusIdsFromRequest) {
                return in_array($bonus->id, $bonusIdsFromRequest->values()->toArray(), true) &&
                    $bonus->bonusValue !== 0.00;
            });
            $evaluationBonusAggregation = $this->mountEvaluationBonus($filteredBonus);
            data_set($tradeInServiceStructure, 'evaluationsBonus', $evaluationBonusAggregation);

            $totalBonusValue = $filteredBonus->sum('bonusValue');
            if ($deviceEvaluation->price < ($deviceEvaluation->price + $totalBonusValue)) {
                data_set($tradeInServiceStructure, 'price', ($deviceEvaluation->price + $totalBonusValue));
            }
        }

        return $tradeInServiceStructure;
    }

    private function validateService(array $service): bool
    {
        $rules = [
            'deviceId' => 'required|int',
            'imei' => 'required|string',
            'networkId' => 'required|int',
            'questions' => 'required'
        ];

        $validator = Validator::make($service, $rules);
        throw_if($validator->fails(), new ModelInvalidException($validator->errors()));

        return true;
    }

    private function getDevicesNetwork($deviceId, $networkId): ?DevicesNetwork
    {
        return DevicesNetwork::where('deviceId', $deviceId)
            ->where('networkId', $networkId)
            ->first();
    }

    private function mountEvaluationBonus(Collection $evaluationBonus): array
    {
        $toMongo = [];
        $evaluationBonus->each(static function (EvaluationsBonus $bonus) use (&$toMongo) {
            $toMongo[] = $bonus->toMongoAggregation();
        });
        return $toMongo;
    }
}

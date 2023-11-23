<?php

namespace Buyback\Repositories;

use Buyback\Models\Evaluation;
use Buyback\Models\Quiz;
use Illuminate\Database\Eloquent\Builder;

use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class EvaluationRepository extends BaseRepository
{
    protected $model = Evaluation::class;

    public function findOneEvaluationByDeviceNetworkId(int $deviceNetworkId): ?Evaluation
    {
        return $this->createModel()->where('deviceNetworkId', $deviceNetworkId)->first();
    }

    public function createEvaluation(array $data, Quiz $quiz)
    {
        $evaluation = new $this->model;
        $evaluation->fill($data)->validate();
        $evaluation->quiz()->associate($quiz)->save();

        return $evaluation;
    }

    public function updateEvaluation(Evaluation $evaluation, array $data): Evaluation
    {
        $evaluation->fill($data);
        $evaluation->restore();
        $evaluation->save();

        return $evaluation;
    }

    public function getDevicesEvaluationsAndFilter($filters = [], $networks = []): Builder
    {
        $labelFilter   = data_get($filters, 'label', '');
        $networkFilter = data_get($filters, 'network', null);

        $devices = Evaluation::query()
            ->whereHas('devicesNetwork.device', static function (Builder $device) use ($labelFilter) {
                $device->where('label', 'like', "%{$labelFilter}%");
            })
            ->whereHas('devicesNetwork.network', static function (Builder $network) use ($networkFilter, $networks) {
                if ($networkFilter === null) {
                    return $network->whereIn('id', $networks);
                }
                    $network->whereIn('id', $networks)
                        ->whereIn('label', $networkFilter);
            })
            ->with('devicesNetwork.device')
            ->with('devicesNetwork.network');

        return $devices;
    }
}

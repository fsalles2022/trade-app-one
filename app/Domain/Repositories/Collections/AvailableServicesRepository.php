<?php


namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Database\Eloquent\Model;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Service;

class AvailableServicesRepository extends BaseRepository
{
    protected $model = AvailableService::class;

    public function addServices(array $attributes): void
    {
        $networkId         = data_get($attributes, 'networkId');
        $availableServices = data_get($attributes, 'availableServices');
        foreach ($availableServices as $sector => $operators) {
            foreach ($operators as $operator => $operations) {
                foreach ($operations as $operation) {
                    $service_key = Service::where([
                        'sector' => $sector,
                        'operator' => $operator,
                        'operation' => $operation
                    ])->first();
                    $this->create([
                        'serviceId' => $service_key->id,
                        'networkId' => $networkId
                    ]);
                }
            }
        }
    }
}

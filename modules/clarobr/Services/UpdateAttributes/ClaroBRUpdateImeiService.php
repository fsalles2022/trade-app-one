<?php


namespace ClaroBR\Services\UpdateAttributes;

use ClaroBR\Connection\SivConnection;
use Illuminate\Support\Collection;

class ClaroBRUpdateImeiService implements ClaroBRUpdateAttributes
{

    private $sivConnection;

    public function __construct(SivConnection $sivConnection)
    {
        $this->sivConnection = $sivConnection;
    }

    public function update(array $service): ?Collection
    {
        $serviceId = data_get($service, 'operatorIdentifiers.servico_id');
        $imei      = data_get($service, 'imei');
        $result    = $this->sivConnection->updateImei($serviceId, ['imei' => $imei]);
        return collect(['response' => $result->toArray()]);
    }
}

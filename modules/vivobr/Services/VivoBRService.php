<?php

namespace VivoBR\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Facades\UserPolicies;
use VivoBR\Connection\SunConnection;
use VivoBR\Enumerators\VivoDomains;

class VivoBRService
{
    protected $sunConnection;

    public function __construct(SunConnection $sunConnection)
    {
        $this->sunConnection = $sunConnection;
    }

    public function domains($operation = null): array
    {
        $dueDates = collect(VivoDomains::DUE_DATES);
        $banks    = collect(VivoDomains::BANKS);

        $portabilityOperators = VivoDomains::ANOTHER_PORTABILITY_OPERATORS;

        return compact('dueDates', 'banks', 'portabilityOperators');
    }

    public function getProducts($filters = []): Collection
    {
        $network     = data_get($filters, 'network', NetworkEnum::CEA);
        $sunResponse = $this->sunConnection->selectCustomConnection($network)->listPlans(['ddd' => '11']);

        return VivoBrMapPlansService::map($sunResponse->toArray());
    }

    public function getPortabilityOperators(): Collection
    {
        $network  = UserPolicies::getNetworksAuthorized()->first();
        $response = $this->sunConnection->selectCustomConnection($network->slug)->portabilityOperators();

        return $response->getStatus() === Response::HTTP_OK
            ? self::portabilityOperatorsAdapter($response->toArray())
            : collect([]);
    }

    public static function portabilityOperatorsAdapter(array $array): Collection
    {
        $operators  = data_get($array, 'operadoras', []);
        $collection = collect($operators);

        return $collection->map(static function ($item) {
            return ['value' => $item['id'], 'label' => $item['nome']];
        });
    }
}

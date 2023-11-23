<?php

namespace VivoBR\Services;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\ContestBehavior;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;
use VivoBR\Connection\SunConnection;
use VivoBR\Enumerators\SunStatus;

class VivoContest implements ContestBehavior
{
    protected $connection;

    public function __construct(SunConnection $connection)
    {
        $this->connection = $connection;
    }

    public function contestService(Service $service, array $payload = [])
    {
        $planSlugM4U = data_get($service, 'planSlug');

        if ($planSlugM4U !== null) {
            return $this->contestVivoTradeup($service, $payload);
        }

        return $this->contestVivoSun($service, $payload);
    }

    private function contestVivoTradeup(Service $service, array $payload): array
    {
        //TODO Implementar consultar por external id na api da M4U.
        return ['service' => $service->toArray(), 'message' => 'Contestação não realizada, m4u.'];
    }

    private function contestVivoSun(Service $service, array $payload): array
    {
        $sunIds     = data_get($service, 'operatorIdentifiers');
        $connection = data_get($service->sale->pointOfSale, 'network.slug');
        throw_if(empty($sunIds), new ServiceNotIntegrated());
        $response = $this->connection->selectCustomConnection($connection)->querySales(['id' => $sunIds['idVenda']])->toArray();
        $response = VivoSaleAPIFilter::filter($response, $sunIds);

        $toUpdate       = [];
        $sunStatus      = data_get($response->service, 'status');
        $msisdn         = data_get($response->service, 'numeroAcesso');
        $backofficeVivo = collect(data_get($response->sale, 'observacoes', []))->last();
        $backofficeVivo = data_get($backofficeVivo, 'observacao', '');
        $imeiLog        = data_get($service, 'imeiLog', null);
        if (! empty($imeiLog)) {
            $toUpdate['imei']    = $imeiLog;
            $toUpdate['imeiLog'] = '';
        }
        if ($sunStatus) {
            $status = data_get(SunStatus::ORIGINAL_STATUS, $sunStatus);
        } else {
            $status = ServiceStatus::ACCEPTED;
        }
        $toUpdate['status'] = $status;
        $toUpdate['msisdn'] = $msisdn;

        $service->update($toUpdate);
        return ['service' => $service->toArray(), 'message' => $backofficeVivo];
    }
}

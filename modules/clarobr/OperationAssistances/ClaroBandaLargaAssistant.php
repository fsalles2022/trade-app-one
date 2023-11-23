<?php

namespace ClaroBR\OperationAssistances;

use ClaroBR\Adapters\SivResponseAdapter;
use ClaroBR\Connection\SivConnectionInterface;
use Illuminate\Http\JsonResponse;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

class ClaroBandaLargaAssistant implements ClaroAssistantBehavior
{

    protected $connection;
    protected $saleRepository;

    public function __construct(SivConnectionInterface $sivConnection, SaleRepository $saleRepository)
    {
        $this->connection     = $sivConnection;
        $this->saleRepository = $saleRepository;
    }

    public function activate(Service $service, array $payload = [])
    {
        $this->saleRepository->pushLogService($service, $payload);

        if ($this->servicoIdNotExists($service)) {
            throw new ServiceNotIntegrated();
        }

        $response = $this->activateService($service, $payload);

        $this->saleRepository->pushLogService($service, $response->toArray());

        if ($this->saleContainsProtocolAndStatus($response)) {
            $this->updateServiceToAccepted($service);

            $msisdn = $this->updateServiceMsisdnFromSivSale($service);

            return $this->adaptResponseWithMsisdn($response, $msisdn);
        }

        return (new SivResponseAdapter($response))->adapt();
    }

    private function servicoIdNotExists(Service $service)
    {
        $servicoId = data_get($service->operatorIdentifiers, 'servico_id');
        return is_null($servicoId);
    }

    private function activateService($service, $payload): \TradeAppOne\Domain\HttpClients\Responseable
    {
        $servicoId = data_get($service->operatorIdentifiers, 'servico_id');
        $msisdn    = data_get($service, 'msisdn');
        return $this->connection->activate($servicoId, $msisdn, $payload);
    }

    private function saleContainsProtocolAndStatus(RestResponse $response): bool
    {
        $responseArray = $response->toArray();
        return (array_has($responseArray, 'data.protocol') && array_has($responseArray, 'data.status'));
    }

    private function updateServiceToAccepted(Service $service): void
    {
        $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
    }

    private function updateServiceMsisdnFromSivSale(Service $service)
    {
        $vendaId = data_get($service->operatorIdentifiers, 'venda_id');
        throw_if(is_null($vendaId), new ServiceNotIntegrated());

        $serviceInSiv = $this->connection->queryUserSales($vendaId)->toArray();
        $msisdn       = data_get($serviceInSiv, 'data.data.0.services.0.numero_acesso');
        $this->saleRepository->updateService($service, ['msisdn' => $msisdn]);
        return $msisdn;
    }

    private function adaptResponseWithMsisdn($response, $msisdn): JsonResponse
    {
        $adapted = new SivResponseAdapter($response);
        $adapted->pushAttributes(['msisdn' => $msisdn]);
        return $adapted->adapt();
    }
}

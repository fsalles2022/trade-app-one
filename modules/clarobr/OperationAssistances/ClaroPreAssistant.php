<?php

namespace ClaroBR\OperationAssistances;

use ClaroBR\Adapters\SivResponseAdapter;
use ClaroBR\Connection\SivConnectionInterface;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

class ClaroPreAssistant implements ClaroAssistantBehavior
{
    protected $connection;
    protected $saleRepository;

    public function __construct(SivConnectionInterface $sivConnection, SaleRepository $saleRepository)
    {
        $this->connection     = $sivConnection;
        $this->saleRepository = $saleRepository;
    }

    /** @throws */
    public function activate(Service $service, array $extraPayload = [])
    {
        $servicoId = $service->operatorIdentifiers['servico_id'];
        $vendaId   = $service->operatorIdentifiers['venda_id'];
        $msisdn    = data_get($service, 'msisdn');
        $this->saleRepository->pushLogService($service, $extraPayload);
        throw_if(is_null($servicoId), new ServiceNotIntegrated());
        $response = $this->connection->activate($servicoId, $msisdn, $extraPayload);
        $this->saleRepository->pushLogService($service, $response->toArray());
        $adapted = new SivResponseAdapter($response);
        if ($this->checkSaleIsActivatedByPayload($response)) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
            $adapted->pushAttributes(['message' => trans('siv::messages.activation.claro_pre.message')]);
        }

        if ($service->mode == Modes::PORTABILITY) {
            $msisdn = $this->requestProvisionalNumber($vendaId);
            $this->saleRepository->updateService($service, ['msisdn' => $msisdn]);
            $adapted->pushAttributes(['provisionalNumber' => $msisdn]);
            return $adapted->adapt();
        }

        if ($service->mode == Modes::ACTIVATION) {
            $serviceInSiv = $this->connection->queryUserSales($vendaId)->toArray();
            $msisdn       = data_get($serviceInSiv, 'data.data.0.services.0.numero_acesso');
            $this->saleRepository->updateService($service, ['msisdn' => $msisdn]);
            $adapted->pushAttributes(['msisdn' => $msisdn]);
            return $adapted->adapt();
        }

        return $adapted->adapt();
    }

    public function checkSaleIsActivatedByPayload(RestResponse $response): bool
    {
        try {
            $responseArray = $response->toArray();
            return isset($responseArray['data']['protocol'], $responseArray['data']['status']);
        } catch (\ErrorException $exception) {
            return false;
        }
    }

    public function requestProvisionalNumber(string $vendaId): ?string
    {
        $serviceInSiv = $this->connection->queryUserSales($vendaId)->toArray();
        return data_get($serviceInSiv, 'data.data.0.services.0.numero_acesso');
    }
}

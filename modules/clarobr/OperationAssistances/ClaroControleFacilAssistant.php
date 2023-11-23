<?php

namespace ClaroBR\OperationAssistances;

use ClaroBR\Adapters\SivResponseAdapter;
use ClaroBR\Connection\SivConnectionInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

class ClaroControleFacilAssistant implements ClaroAssistantBehavior
{
    const HTTP_STATUS_TOKEN_TO_CHANGE_HOLDER = 206;
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
        $servicoId = data_get($service->operatorIdentifiers, 'servico_id');
        $msisdn    = data_get($service, 'msisdn');
        $response  = $this->connection->activate($servicoId, $msisdn, $extraPayload);

        $this->saleRepository->pushLogService($service, $response->toArray());

        $adapter = new SivResponseAdapter($response);
        $adapter->pushAttributes(array_filter([
            'pid' => $servicoId,
            'remoteSale' => Arr::get($extraPayload, 'remoteSale'),
            'urlOrigin'  => Arr::get($extraPayload, 'urlOrigin')
        ]));

        $this->checkSaleIsActivatedByPayload($response)
            ? $this->withSuccess($service)
            : $this->withError($service, $adapter);

        return $adapter->adapt();
    }

    public function checkSaleIsActivatedByPayload(Responseable $response): bool
    {
        try {
            $responseArray = $response->toArray();
            $statusSuccess = in_array($response->getStatus(), [Response::HTTP_PARTIAL_CONTENT, Response::HTTP_OK]);

            if (isset($responseArray['data']['protocolo'], $responseArray['data']['idM4U']) && $statusSuccess) {
                return true;
            }

            return isset($responseArray['data']['protocol'], $responseArray['data']['status']) && $statusSuccess;
        } catch (\ErrorException $exception) {
            return false;
        }
    }

    protected function withSuccess(Service $service): void
    {
        $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
    }

    protected function withError(Service $service, SivResponseAdapter $response): void
    {
        if ($response->isSimCardInvalid()) {
            return;
        }

        $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
    }
}

<?php

namespace OiBR\Assistance;

use Illuminate\Http\Response;
use OiBR\Adapters\OiBRResponseAdapter;
use OiBR\Connection\OiBRConnection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;

class OiControleBoletoAssistance implements AssistanceBehavior
{
    protected $connection;
    protected $saleRepository;

    public function __construct(SaleService $saleRepository, OiBRConnection $connection)
    {
        $this->connection     = $connection;
        $this->saleRepository = $saleRepository;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $oiBrResponse = $this->connection->controleBoletoSale($service);
        if ($oiBrResponse->getStatus() == Response::HTTP_CREATED) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
        }
        $response = new OiBRResponseAdapter($oiBrResponse);
        $this->saleRepository->pushLogService($service, $oiBrResponse->toArray());
        return $response->adapt();
    }
}

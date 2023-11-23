<?php

namespace TimBR\Assistance;

use Illuminate\Support\Str;
use TimBR\Assistance\TimBROperationsAssistances\TimBRAssistances;
use TimBR\Connection\TimBRConnection;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class TimBRSaleAssistance implements AssistanceBehavior
{
    protected $saleRepository;
    protected $connection;

    public function __construct(SaleRepository $saleRepository, TimBRConnection $connection)
    {
        $this->saleRepository = $saleRepository;
        $this->connection     = $connection;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $assistance = TimBRAssistances::make($service->operation);

        return $assistance->activate($service)->adapt();
    }
}

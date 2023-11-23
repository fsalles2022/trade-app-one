<?php

namespace OiBR\Assistance;

use OiBR\Connection\OiBRConnection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class OiBRSaleAssistance implements AssistanceBehavior
{
    const ASSISTANCES = [
        Operations::OI_CONTROLE_CARTAO => OiControleCartaoAssistance::class,
        Operations::OI_CONTROLE_BOLETO => OiControleBoletoAssistance::class
    ];

    protected $connection;
    protected $saleRepository;

    public function __construct(SaleRepository $saleRepository, OiBRConnection $connection)
    {
        $this->connection     = $connection;
        $this->saleRepository = $saleRepository;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $operation  = $service->operation;
        $assistance = resolve(self::ASSISTANCES[$operation]);
        return $assistance->integrateService($service);
    }
}

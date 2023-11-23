<?php

namespace Movile\Assistance;

use Movile\Adapters\Request\SubscribeRequestAdapter;
use Movile\Adapters\Response\SubscribeResponseAdapter;
use Movile\Connection\MovileConnection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class MovileSaleAssistance implements AssistanceBehavior
{
    protected $connection;
    protected $repository;

    public function __construct(MovileConnection $connection, SaleRepository $repository)
    {
        $this->repository = $repository;
        $this->connection = $connection;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        $adapted            = SubscribeRequestAdapter::adapt($service);
        $responseFromMovile = $this->connection->subscribe($adapted);
        $this->repository->pushLogService($service, $responseFromMovile->toArray());
        $responseAdapted = new SubscribeResponseAdapter($responseFromMovile);

        if ($responseAdapted->isSuccess()) {
            $this->repository->updateService($service, ['status' => ServiceStatus::APPROVED]);
        } else {
            $this->repository->updateService($service, ['status' => ServiceStatus::REJECTED]);
        }

        return $responseAdapted->adapt();
    }
}

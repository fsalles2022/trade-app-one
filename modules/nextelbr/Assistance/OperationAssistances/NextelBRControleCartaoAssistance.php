<?php

namespace NextelBR\Assistance\OperationAssistances;

use NextelBR\Adapters\Request\AdhesionRequestAdapter;
use NextelBR\Adapters\Request\AuthenticationCodeRequestAdapter;
use NextelBR\Adapters\Response\AdhesionResponseAdapter;
use NextelBR\Adapters\Response\ModalLinResponseAdapter;
use NextelBR\Connection\M4uModal\NextelBRModalConnection;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\AssistanceBehavior;

class NextelBRControleCartaoAssistance implements AssistanceBehavior
{
    protected $connection;
    protected $nextelConnection;
    protected $repository;

    public function __construct(
        NextelBRModalConnection $connection,
        NextelBRConnection $nextelConnection,
        SaleRepository $repository
    ) {
        $this->nextelConnection = $nextelConnection;
        $this->connection       = $connection;
        $this->repository       = $repository;
    }

    public function integrateService(Service $service, array $payload = [])
    {
        if (data_get($payload, 'executed') == true) {
            $adapted          = AdhesionRequestAdapter::adapt($service);
            $protocolo        = data_get($service->operatorIdentifiers, 'protocolo');
            $resultOfAdhesion = $this->nextelConnection->adhesion($protocolo, $adapted);
            $this->repository->pushLogService($service, $resultOfAdhesion->toArray());
            $resultOfAdhesion = new AdhesionResponseAdapter($resultOfAdhesion);
            if ($resultOfAdhesion->isSuccess()) {
                $this->repository->updateService($service, ['status' => ServiceStatus::APPROVED]);
                $msisdn = data_get($service, 'msisdn');
                $resultOfAdhesion->pushAttributes(['msisdn' => $msisdn]);
            }
            return $resultOfAdhesion;
        }
        return $this->generateAuthCode($service);
    }

    private function generateAuthCode(Service $service)
    {
        $authenticationRequest = AuthenticationCodeRequestAdapter::adapt($service);
        $resultAuthentication  = $this->connection->getAuthenticationCode($authenticationRequest);
        $this->repository->pushLogService($service, $resultAuthentication->toArray());
        return new ModalLinResponseAdapter($resultAuthentication);
    }
}

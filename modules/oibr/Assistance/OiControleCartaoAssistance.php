<?php

namespace OiBR\Assistance;

use Illuminate\Http\Response;
use OiBR\Adapters\OiBRResponseAdapter;
use OiBR\Components\OiBRUUID;
use OiBR\Connection\OiBRConnection;
use OiBR\Enumerators\OiBRCartaoStatus;
use OiBR\Exceptions\OiBRStatusUnreach;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;

class OiControleCartaoAssistance implements AssistanceBehavior
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
        $uuid    = OiBRUUID::genUuid();
        $service = $this->saleRepository->updateService($service, ['operatorIdentifiers' => ['uuid' => $uuid]]);

        $oiBrResponse = $this->connection->controleCartaoSale($service);


        $this->saleRepository->pushLogService($service, $oiBrResponse->toArray());

        if ($service->mode == Modes::ACTIVATION) {
            if ($oiBrResponse->getStatus() == Response::HTTP_ACCEPTED) {
                $service    = $this->searchOiCartaoStatus($service);
                $message    = [
                    'message' => trans('oiBR::messages.controle_cartao.success'),
                    'msisdn'  => $service->msisdn
                ];
                $httpStatus = Response::HTTP_OK;
                if ($service->status === ServiceStatus::ACCEPTED) {
                    $message = ['message' => trans('oiBR::messages.controle_cartao.pending')];
                } elseif ($service->status === ServiceStatus::REJECTED) {
                    $message    = ['message' => trans('oiBR::messages.controle_cartao.failure')];
                    $httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;
                }
                return OiBRResponseAdapter::build($message, $httpStatus);
            }
        }
        if ($oiBrResponse->getStatus() == Response::HTTP_OK) {
            $this->saleRepository->updateStatusService($service, ServiceStatus::APPROVED);
            $this->saleRepository->updateService($service, [
                'operatorIdentifiers' => [
                    'ref' => data_get($oiBrResponse->toArray(), 'ref')
                ]
            ]);
        }
        $response = (new OiBRResponseAdapter($oiBrResponse));
        $response->pushAttributes(['msisdn' => $service->msisdn]);
        
        if ($service->mode === Modes::MIGRATION) {
            $response->pushAttributes(['message' => trans('oiBR::messages.controle_cartao.migration_success')]);
        }

        return $response->adapt();
    }

    public function searchOiCartaoStatus($service)
    {
        $uuid             = data_get($service, 'operatorIdentifiers.uuid', '');
        $oiStatusResponse = $this->connection->controleCartaoStatus($uuid)->toArray();
        if ($oiStatus = data_get($oiStatusResponse, 'status')) {
            $status = OiBRCartaoStatus::translate($oiStatus);
            return $this->saleRepository->updateService($service, [
                'status' => $status,
                'statusThirdParty' => $oiStatus,
                'msisdn' => data_get($oiStatusResponse, 'msisdn')
            ]);
        }
        throw new OiBRStatusUnreach();
    }
}

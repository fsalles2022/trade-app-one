<?php

namespace ClaroBR\OperationAssistances;

use ClaroBR\Adapters\SivResponseAdapter;
use ClaroBR\Connection\SivConnectionInterface;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\ImeiConstant;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;

trait ClaroActivationAssistance
{
    protected $connection;
    protected $saleRepository;

    public function __construct(SivConnectionInterface $sivConnection, SaleRepository $saleRepository)
    {
        $this->connection     = $sivConnection;
        $this->saleRepository = $saleRepository;
    }

    /** @throws */
    public function activation(Service $service, array $extraPayload = [])
    {
        $servicoId = $service->operatorIdentifiers['servico_id'];
        $vendaId   = $service->operatorIdentifiers['venda_id'];
        throw_if(is_null($servicoId), new ServiceNotIntegrated());

        if (str_contains(data_get($service, 'device.model'), 's20')) {
            $extraPayload['options']['faseado'] = 1;
        }

        if ($service->imei === ImeiConstant::DEFAULT) {
            unset($service->imei);
        }

        $requestTwoMsisdn = request()->get('msisdn');
        $portedNumber     = $service->portedNumber;
        $migrationMsisdn  = $service->msisdn;

        if ($service->operation === Operations::CLARO_CONTROLE_BOLETO &&
            $requestTwoMsisdn === null &&
            ($portedNumber !== null || $migrationMsisdn !== null)
        ) {
            $requestTwoMsisdn = $portedNumber === null ? $migrationMsisdn : $portedNumber;
        }

        $response = $this->connection->activate($servicoId, $requestTwoMsisdn, $extraPayload);
        $this->saleRepository->pushLogService($service, $response->toArray());

        $msisdn = $this->getMsisdn($vendaId);

        if ($msisdn) {
            $this->saveMsisdn($service, MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $msisdn));
        }

        if ($this->checkSaleIsActivatedByPayload($response)) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
        }

        $adapted = new SivResponseAdapter($response);
        $adapted->pushAttributes(['pid' => $servicoId]);

        if ($service->mode == Modes::PORTABILITY) {
            $adapted->pushAttributes(['provisionalNumber' => $msisdn]);
            return $adapted->adapt();
        }

        if ($service->mode == Modes::ACTIVATION) {
            $adapted->pushAttributes(['msisdn' => $msisdn]);
            return $adapted->adapt();
        }

        return $adapted->adapt();
    }

    public function getMsisdn(?string $vendaId): ?string
    {
        $serviceInSiv = $this->connection->queryUserSales($vendaId)->toArray();
        return data_get($serviceInSiv, 'data.data.0.services.0.numero_acesso');
    }

    public function saveMsisdn(Service $service, ?string $msisdn): Service
    {
        return $this->saleRepository->updateService($service, ['msisdn' => $msisdn]);
    }

    public function checkSaleIsActivatedByPayload(RestResponse $response): bool
    {
        $responseArray = $response->toArray();
        $protocol      = data_get($responseArray, 'data.protocol');
        $status        = data_get($responseArray, 'data.status');

        return $protocol && $status;
    }
}

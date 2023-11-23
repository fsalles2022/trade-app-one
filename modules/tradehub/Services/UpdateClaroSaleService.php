<?php

declare(strict_types=1);

namespace Tradehub\Services;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Enumerators\SivStatus;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;

class UpdateClaroSaleService implements TradeHubSaleUpdate
{
    /** @var SaleService */
    private $saleService;

    /** @var SivConnection */
    private $sivConnection;

    public function __construct(SaleService $saleService, SivConnection $sivConnection)
    {
        $this->saleService = $saleService;
        $this->sivConnection = $sivConnection;
    }

    public function getServiceAttributesToUpdate(array $tradeHubRequestPayload, Sale $sale, Service $service): array
    {
        $serviceAttributesToUpdate = [];
        $sivServiceAttributesToUpdate = [];

        if ($service->operation === Operations::CLARO_CONTROLE_FACIL) {
            if ($service->mode === Modes::PORTABILITY) {
                $msisdn = data_get($tradeHubRequestPayload, 'order.additionalData.provisionalNumber');

                // Necessary for don`t broke flow, this flow is used whe admin user change sale status manually
                if (!empty($msisdn)) {
                    $serviceAttributesToUpdate['provisionalNumber'] = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $msisdn);
                    $sivServiceAttributesToUpdate['numero_acesso'] = '+' . MsisdnHelper::addCountryCode(MsisdnHelper::BR, $msisdn);
                }
            }

            if ($service->mode === Modes::ACTIVATION) {
                $msisdn = data_get($tradeHubRequestPayload, 'order.paymentData.mobile.MSISDN');

                // Necessary for don`t broke flow, this flow is used whe admin user change sale status manually
                if (!empty($msisdn)) {
                    $serviceAttributesToUpdate['msisdn'] = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $msisdn);
                    $sivServiceAttributesToUpdate['numero_acesso'] = '+' . MsisdnHelper::addCountryCode(MsisdnHelper::BR, $msisdn);
                }
            }

            $orderProtocol = data_get($tradeHubRequestPayload, 'order.additionalData.protocol');
            $saleIdM4u = data_get($tradeHubRequestPayload, 'order.additionalData.saleIdM4u');

            if (!empty($orderProtocol)) {
                $sivServiceAttributesToUpdate['codigo_proposta'] = $orderProtocol;
            }

            if (!empty($saleIdM4u)) {
                $sivServiceAttributesToUpdate['codigo_autorizacao'] = $saleIdM4u;
            }
        }

        $sivSaleId = data_get($service, 'operatorIdentifiers.venda_id');
        $sivServiceId = data_get($service, 'operatorIdentifiers.servico_id');

        // Required to update sale in SIV Legacy
        if (!empty($sivSaleId) && !empty($sivServiceId)) {
            if ($service->status === ServiceStatus::APPROVED) {
                $sivServiceAttributesToUpdate['status'] = SivStatus::APPROVED_STATUS;
            }

            if (in_array($service->status, [ ServiceStatus::CANCELED, ServiceStatus::REJECTED ])) {
                $sivServiceAttributesToUpdate['status'] = SivStatus::CANCELED_STATUS;
            }

            $response = $this->sivConnection->update((string) $sivSaleId, (string) $sivServiceId, array_filter($sivServiceAttributesToUpdate));

            if ($response->isSuccess() === false) {
                $this->saleService->pushLogService($service, [
                    'createdAt' => now()->format('Y-m-d H:i:s'),
                    'error' => $response->toArray(),
                    'message' => 'Falha ao atualizar a venda no SIV Legado - via WebHook TradeHUB',
                ]);
            }

            if ($response->isSuccess()) {
                $this->saleService->pushLogService($service, [
                    'createdAt' => now()->format('Y-m-d H:i:s'),
                    'attributes' => $sivServiceAttributesToUpdate,
                    'message' => 'Atualização da venda no SIV Legado - via WebHook TradeHUB',
                ]);
            }
        }

        return $serviceAttributesToUpdate;
    }
}

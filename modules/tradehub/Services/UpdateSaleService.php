<?php

declare(strict_types=1);

namespace Tradehub\Services;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;
use Tradehub\Enumerators\TradeHubStatus;
use Tradehub\Exceptions\TradeHubExceptions;

class UpdateSaleService
{
    /** @var SaleService */
    private $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /** Necessary to make dependency in lazy mode */
    private function getServiceStrategyByService(Service $service): ?TradeHubSaleUpdate
    {
        $operator = $service->operator;

        if ($operator === Operations::CLARO) {
            return app()->make(UpdateClaroSaleService::class);
        }

        return null;
    }

    /**
     * @param mixed[] $request
     * @return string[]
     */
    public function update(array $request): array
    {
        $tradeHubCheckoutProductItemId = (string) data_get($request, 'order.checkoutProductItemId');

        /** @var Sale $sale */
        /** @var Service $service */
        [
            $sale,
            $service
        ] = $this->getSaleAndService($tradeHubCheckoutProductItemId);

        $this->updateService($request, $sale, $service);

        return [
            'message' => trans('tradehub::messages.receiveWebHook.success'),
            'success' => true,
        ];
    }

    private function getSaleAndService(string $tradeHubCheckoutProductItemId): array
    {
        if (empty($tradeHubCheckoutProductItemId)) {
            throw (new TradeHubExceptions)->checkoutProductItemEmpty();
        }

        $sales = $this->saleService->filterAll([
            'tradeHubCheckoutProductItemId' => $tradeHubCheckoutProductItemId
        ], 0, 1);

        if ($sales->isEmpty()) {
            throw (new TradeHubExceptions)->saleNotFound();
        }

        /** @var Sale $sale */
        $sale = $sales->first();
        $services = $sale->services;

        /** @var Service $service */
        foreach ($services as $service) {
            $serviceTradeHubCheckoutProductItemId = data_get($service, 'tradeHub.checkoutProductItemId');

            // Find service by checkoutProductItemId
            if ($tradeHubCheckoutProductItemId === $serviceTradeHubCheckoutProductItemId) {
                return [
                    $sale,
                    $service
                ];
            }
        }

        // Double check, case service not found.
        throw (new TradeHubExceptions)->saleNotFound();
    }

    private function updateService(array $request, Sale $sale, Service $service): void
    {
        $tradeHubServiceStatus = (string) data_get($request, 'order.status.current');

        $serviceStatus = $service->status;

        $newServiceStatus = TradeHubStatus::TRANSLATE[$tradeHubServiceStatus] ?? $serviceStatus;

        if ($newServiceStatus !== $serviceStatus) {
            $service = $this->saleService->updateService($service, [
                'status' => $newServiceStatus
            ]);

            $this->saleService->pushLogService($service, [
                'createdAt' => now()->format('Y-m-d H:i:s'),
                'message' => 'Atualização de status via WebHook TradeHUB de:' . $serviceStatus . ' para:' . $newServiceStatus
            ]);
        }

        $strategy = $this->getServiceStrategyByService($service);

        if ($strategy instanceof TradeHubSaleUpdate) {
            $attributesToUpdateByStrategy = $strategy->getServiceAttributesToUpdate($request, $sale, $service);

            $this->saleService->updateService($service, $attributesToUpdateByStrategy);

            $this->saleService->pushLogService($service, [
                'createdAt' => now()->format('Y-m-d H:i:s'),
                'attributes' => $attributesToUpdateByStrategy,
                'strategy' => get_class($strategy),
                'message' => 'Atualização da venda via WebHook TradeHUB',
            ]);
        }
    }
}

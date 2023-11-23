<?php

namespace ClaroBR\OperationAssistances;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Enumerators\SivStatus;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use Tradehub\Services\TradeHubService;

class ClaroControleFacilV3Assistant implements ClaroAssistantBehavior
{
    /** @var SivConnectionInterface */
    protected $sivConnection;

    /** @var SaleRepository */
    protected $saleRepository;

    /** @var TradeHubService */
    protected $tradeHubService;

    public function __construct(
        SivConnectionInterface $sivConnection,
        SaleRepository $saleRepository,
        TradeHubService $tradeHubService
    ) {
        $this->sivConnection   = $sivConnection;
        $this->saleRepository  = $saleRepository;
        $this->tradeHubService = $tradeHubService;
    }

    /** @throws */
    public function activate(Service $service, array $extraPayload = [])
    {
        $service = $this->processOrder($service);

        $tradeHubOrder = $this->tradeHubService->checkoutActivateService(data_get($service, 'tradeHub.saleOfServiceId'));

        $this->updateSivStatus($service);

        if ($this->checkSaleIsActivatedByPayload($tradeHubOrder)) {
            $this->withSuccess($service, $tradeHubOrder);

            return \response()->json([
                'pid' => data_get($tradeHubOrder, 'response.details.orders.0.detailOrder.additionalData.orderNumber'),
                'remoteSale' => data_get($tradeHubOrder, 'response.details.orders.0.detailOrder.additionalData.linkIframePayment'),
            ]);
        }

        $this->withError($service, $tradeHubOrder);

        return \response()->json([
            'error' => true,
            'message' => data_get($tradeHubOrder, 'response.details.orders.0.detailOrder.additionalData.exception.errorExceptionMessage', '')
        ], Response::HTTP_PRECONDITION_FAILED);
    }

    private function processOrder(Service $service): Service
    {
        if (data_get($service, 'tradeHub.orderGenerated') === true) {
            return $service;
        }

        $service = $this->addItemToCart($service);

        $paymentOptionId = $this->getPaymentOption($service);

        $this->generateOrder($service, $paymentOptionId);

        $tradeHub                           = data_get($service, 'tradeHub');
        $tradeHub['productPaymentOptionId'] = $paymentOptionId;
        $tradeHub['orderGenerated']         = true;

        return $this->saleRepository->updateService($service, ['tradeHub' => $tradeHub]);
    }

    private function addItemToCart(Service $service): Service
    {
        $checkoutProductItemId = data_get($service, 'tradeHub.checkoutProductItemId');
        $saleOfServiceId       = data_get($service, 'tradeHub.saleOfServiceId');
        $productId             = data_get($service, 'tradeHub.product.id');
        $promotionId           = data_get($service, 'tradeHub.promotion.id');

        $response = $this->tradeHubService->checkoutItemAdd(
            $checkoutProductItemId,
            $saleOfServiceId,
            $productId,
            $promotionId
        );

        $checkoutProductItemIdResponse = data_get($response, 'response.checkoutProductItem.id');

        if ($checkoutProductItemIdResponse !== $checkoutProductItemId) {
            $tradeHub                          = data_get($service, 'tradeHub');
            $tradeHub['checkoutProductItemId'] = $checkoutProductItemIdResponse;

            return $this->saleRepository->updateService($service, ['tradeHub' => $tradeHub]);
        }

        return $service;
    }

    private function getPaymentOption(Service $service): ?string
    {
        $saleOfServiceId = data_get($service, 'tradeHub.saleOfServiceId');
        $productId       = data_get($service, 'tradeHub.product.id');
        $promotionId     = data_get($service, 'tradeHub.promotion.id');

        $paymentOptions = $this->tradeHubService->checkoutPaymentOptions($saleOfServiceId);

        $paymentOptionsList = data_get($paymentOptions, 'response.paymentOptions', []);

        foreach ($paymentOptionsList as $paymentOption) {
            $paymentOptionProductId   = data_get($paymentOption, 'productId');
            $paymentOptionPromotionId = data_get($paymentOption, 'promotionId');

            if ($paymentOptionProductId !== $productId && $promotionId !== $paymentOptionPromotionId) {
                continue;
            }

            return data_get($paymentOption, 'paymentOptions.items.0.id');
        }

        return null;
    }

    private function generateOrder(Service $service, string $paymentOptionId): void
    {
        $checkoutProductItemId = data_get($service, 'tradeHub.checkoutProductItemId');
        $saleOfServiceId       = data_get($service, 'tradeHub.saleOfServiceId');

        $this->tradeHubService->checkoutOrder(
            $saleOfServiceId,
            [
                [
                    'checkoutProductItemId' => $checkoutProductItemId,
                    'payment' => [
                        'productPaymentOptionId' => $paymentOptionId,
                        'paymentData' => $this->getPaymentDataByService($service),
                    ]
                ]
            ]
        );
    }

    private function getPaymentDataByService(Service $service): array
    {
        if ($service->mode === Modes::ACTIVATION) {
            return [
                'externalData' => [
                    'sivLegacyServiceId' => data_get($service->operatorIdentifiers, 'servico_id'),
                    'sivLegacySaleId' => data_get($service->operatorIdentifiers, 'venda_id'),
                    'tradeAppOneLegacyServiceId' => data_get($service, 'serviceTransaction'),
                ],
                'mobile' => [
                    'operator' => 'CLARO',
                    'operation' => 'ACTIVATE',
                    'ICCID' => data_get($service, 'iccid'),
                ]
            ];
        }

        if ($service->mode === Modes::PORTABILITY) {
            return [
                'externalData' => [
                    'sivLegacyServiceId' => data_get($service->operatorIdentifiers, 'servico_id'),
                    'sivLegacySaleId' => data_get($service->operatorIdentifiers, 'venda_id'),
                    'tradeAppOneLegacyServiceId' => data_get($service, 'serviceTransaction'),
                ],
                'mobile' => [
                    'operator' => 'CLARO',
                    'operation' => 'PORTABILITY',
                    'MSISDN' => data_get($service, 'portedNumber'),
                    'ICCID' => data_get($service, 'iccid'),
                    'codeAuthorizationPortability' => ! empty($service->portedNumberToken) ? base64_decode($service->portedNumberToken) : null,
                ]
            ];
        }

        if ($service->mode === Modes::MIGRATION) {
            return [
                'externalData' => [
                    'sivLegacyServiceId' => data_get($service->operatorIdentifiers, 'servico_id'),
                    'sivLegacySaleId' => data_get($service->operatorIdentifiers, 'venda_id'),
                    'tradeAppOneLegacyServiceId' => data_get($service, 'serviceTransaction'),
                ],
                'mobile' => [
                    'operator' => 'CLARO',
                    'operation' => 'MIGRATION',
                    'MSISDN' => data_get($service, 'msisdn')
                ]
            ];
        }

        return [];
    }

    protected function updateSivStatus(Service $service): void
    {
        $sivSaleId    = data_get($service, 'operatorIdentifiers.venda_id');
        $sivServiceId = data_get($service, 'operatorIdentifiers.servico_id');

        $this->sivConnection->update(
            $sivSaleId,
            $sivServiceId,
            [
                'status' => SivStatus::PENDENTE_M4U
            ]
        );
    }

    protected function checkSaleIsActivatedByPayload(array $response): bool
    {
        return data_get($response, 'response.details.orders.0.detailOrder.status.current', false) === 'AWAITING_PAYMENT';
    }

    protected function withSuccess(Service $service, array $response): void
    {
        $this->saleRepository->pushLogService($service, [
            'paymentLinkGenerated' => true,
            'orderNumber' => data_get($response, 'response.details.orders.0.detailOrder.additionalData.orderNumber', ''),
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->saleRepository->updateService($service, ['status' => ServiceStatus::ACCEPTED]);
    }

    protected function withError(Service $service, array $response): void
    {
        $this->saleRepository->pushLogService($service, [
            'error' => true,
            'message' => data_get($response, 'response.details.orders.0.detailOrder.additionalData.exception.errorExceptionMessage', ''),
            'createdAt' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->saleRepository->updateService($service, ['status' => ServiceStatus::REJECTED]);
    }
}

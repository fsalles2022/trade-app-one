<?php

declare(strict_types=1);

namespace SurfPernambucanas\Assistances;

use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

/** Represent Recharge */
class SurfPernambucanasRechargeAssistance extends SurfPernambucanasAssistance implements AssistanceBehavior
{
    /** @param mixed[] $payload */
    public function integrateService(Service $service, array $payload = []): PagtelResponseAdapter
    {
        $paymentId = $this->getPaymentId($service, $payload);
        
        $response = $this->recharge($service, $paymentId, $payload);

        $this->updateServiceWithSuccess($response, $service);

        return $response;
    }

    /** @param mixed[] $payload */
    protected function getPaymentId(Service $service, array $payload): string
    {
        $cardNumber = data_get($payload, 'creditCard.cardNumber', '');

        $card = $this->getCardByCardNumber($service->msisdn, $cardNumber);
        
        if ($card === null) {
            return $this->addCard($service, $payload);
        }

        return data_get($card, 'paymentId');
    }

    /** @param mixed[] $payload */
    protected function recharge(Service $service, string $paymentId, array $payload): PagtelResponseAdapter
    {
        $cardCvv         = data_get($payload, 'creditCard.cvv', '');
        $programRecharge = data_get($payload, 'creditCard.program', false);

        $response = $this->pagtelService->recharge(
            $service->msisdn,
            $service->msisdn,
            $this->getPriceInCentsByService($service),
            $paymentId,
            $cardCvv,
            $programRecharge
        );

        $this->throwExceptionIfNotSuccessRequestAndRejectServiceByResponseAdapterAndService($response, $service);

        return $response;
    }
}

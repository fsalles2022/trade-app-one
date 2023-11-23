<?php

declare(strict_types=1);

namespace SurfPernambucanas\Assistances;

use SurfPernambucanas\Adapters\PagtelActivationActivateResponseAdapter;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\DataObjects\CreditCardDTO;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Models\Collections\Service;

/** Represent Activation Pré Pago */
class SurfPernambucanasSmartControlAssistance extends SurfPernambucanasAssistance implements AssistanceBehavior
{
    /** @param mixed[] $payload */
    public function integrateService(Service $service, array $payload = []): PagtelResponseAdapter
    {
        $response = $this->activate($service, $payload);

        $this->updateServiceWithSuccess($response, $service);

        $responsePortin = $this->submitPortin($service);

        if ($responsePortin !== null) {
            $this->updateServiceWithSuccess($responsePortin, $service);
            $response = $responsePortin;
        }

        return $response;
    }

    /** @param mixed[] $payload */
    protected function activate(Service $service, array $payload): PagtelActivationActivateResponseAdapter
    {
        $creditCard = new CreditCardDTO(
            data_get($payload, 'creditCard.cardNumber', ''),
            data_get($payload, 'creditCard.cvv', ''),
            data_get($payload, 'creditCard.year', ''),
            data_get($payload, 'creditCard.month', '')
        );

        $response = $this->pagtelService->activationActivate(
            (string) data_get($service, 'areaCode'),
            data_get($service, 'product', ''),
            data_get($service, 'iccid', ''),
            data_get($service, 'customer.cpf', ''),
            data_get($service, 'customer.firstName') . ' ' . data_get($service, 'customer.lastName'),
            $creditCard,
            data_get($service, 'recurrence', false)
        );

        $this->throwExceptionIfNotSuccessRequestAndRejectServiceByResponseAdapterAndService($response, $service);

        $this->updateServiceMsisdn(
            $service,
            data_get($response->getAdapted(), 'msisdn', '')
        );

        return $response;
    }

    /**
     * @throws \TradeAppOne\Exceptions\BuildExceptions
     */
    protected function submitPortin(Service $service): ?PagtelResponseAdapter
    {
        $mode = data_get($service, 'mode');

        if (! $mode || $mode !== Modes::PORTABILITY) {
            return null;
        }

        $response = $this->pagtelService->submitPortin(
            data_get($service, 'msisdn'),
            data_get($service, 'portedNumber'),
            data_get($service, 'customer.cpf'),
            data_get($service, 'fromOperator', 0),
            data_get($service, 'portinDate'),
            data_get($service, 'customer.firstName') . ' ' . data_get($service, 'customer.lastName')
        );

        $this->throwExceptionIfNotSuccessRequestAndRejectServiceByResponseAdapterAndService($response, $service);

        return $response;
    }

    protected function updateServiceMsisdn(Service $service, string $msisdn): void
    {
        $this->saleRepository->updateService($service, [
            'msisdn' => $msisdn
        ]);
    }
}

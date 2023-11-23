<?php

declare(strict_types=1);

namespace SurfPernambucanas\Assistances;

use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Exceptions\PagtelExceptions;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;

abstract class SurfPernambucanasAssistance
{
    /** @var PagtelService */
    protected $pagtelService;

    /** @var SaleRepository */
    protected $saleRepository;

    public function __construct(PagtelService $pagtelService, SaleRepository $saleRepository)
    {
        $this->pagtelService  = $pagtelService;
        $this->saleRepository = $saleRepository;
    }

    /** @return mixed[]|null */
    protected function getCardByCardNumber(string $msisdn, string $cardNumber): ?array
    {
        $cards = collect($this->getCards($msisdn));

        $key = $cards->search(function (array $card) use ($cardNumber): bool {
            $cardDigFour = data_get($card, 'digFour', '');
            
            return $this->getLastFourDigits($cardNumber) === $cardDigFour;
        });

        if ($key === false) {
            return null;
        }

        return $cards->get($key);
    }

    protected function getLastFourDigits(string $cardNumber): string
    {
        $cardNumber = str_replace(' ', '', $cardNumber);
        
        return mb_substr($cardNumber, -4);
    }

    /** @return array[] */
    protected function getCards(string $msisdn): array
    {
        $response = $this->pagtelService->getCards($msisdn);

        $this->throwExceptionIfNotSuccessRequestByResponseAdapter($response);

        return data_get($response->getAdapted(), 'cards', []);
    }

    /** @param mixed[] $payload */
    protected function addCard(Service $service, array $payload): string
    {
        $response = $this->pagtelService->addCard(
            $service->msisdn,
            data_get($payload, 'creditCard.cardNumber', ''),
            data_get($payload, 'creditCard.cvv', ''),
            data_get($payload, 'creditCard.month', ''),
            data_get($payload, 'creditCard.year', '')
        );

        $this->throwExceptionIfNotSuccessRequestAndRejectServiceByResponseAdapterAndService($response, $service);

        return data_get($response->getAdapted(), 'paymentId', '');
    }

    protected function throwExceptionIfNotSuccessRequestAndRejectServiceByResponseAdapterAndService(
        PagtelResponseAdapter $response,
        Service $service
    ): void {
        try {
            $this->throwExceptionIfNotSuccessRequestByResponseAdapter($response);
        } catch (BuildExceptions $ex) {
            $this->updateServiceWithErrors($response, $service);
            throw $ex;
        }
    }

    /** @throws BuildExceptions
     * @throws \Throwable
     */
    protected function throwExceptionIfNotSuccessRequestByResponseAdapter(PagtelResponseAdapter $response): void
    {
        $message = data_get($response->getOriginal(), 'msg');

        if ($message === null) {
            $message = data_get($response->getOriginal(), 'error.*.message', []);
            $message = data_get($message, '0', 'Erro na operadora.');
        }

        throw_if(
            $response->isSuccess() === false,
            PagtelExceptions::failRequestByMessage($message)
        );
    }

    protected function logService(PagtelResponseAdapter $response, Service $service): void
    {
        $this->saleRepository->pushLogService($service, $response->getOriginal());
    }

    protected function updateServiceWithSuccess(
        PagtelResponseAdapter $response,
        Service $service,
        string $status = null
    ): void {
        $this->logService($response, $service);

        $this->saleRepository->updateService($service, [
            'status' => $status ?? ServiceStatus::APPROVED
        ]);

        $response->pushAttributes($this->getSuccessMessage($service));
    }

    public function updateServiceWithErrors(PagtelResponseAdapter $response, Service $service): void
    {
        $this->logService($response, $service);

        $this->saleRepository->updateService($service, [
            'status' => ServiceStatus::REJECTED
        ]);
    }

    /** @return mixed[] */
    protected function getSuccessMessage(Service $service): array
    {
        return ['message' => trans('surfpernambucanas::messages.activation.' . $service->operation)];
    }

    protected function getPriceInCentsByService(Service $service): string
    {
        $priceInCents = data_get($service, 'price', 0);

        return number_format($priceInCents * 100, 0, '', '');
    }
}

<?php

namespace Gateway\Services;

use Gateway\API\Gateway;
use Gateway\Components\CreditCard;
use Gateway\Components\Customer;
use Gateway\Components\Interest;
use Gateway\Components\Transaction;
use Gateway\Connection\GatewayConnection;
use Gateway\Enumerators\GatewayStatus;
use Gateway\Exceptions\GatewayExceptions;
use Gateway\Helpers\GatewayMethodsEnum;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;

class GatewayService
{
    /** @var GatewayConnection */
    private $gatewayConnection;

    /** @var SaleRepository */
    private $saleRepository;

    public function __construct(GatewayConnection $gatewayConnection, SaleRepository $saleRepository)
    {
        $this->gatewayConnection = $gatewayConnection;
        $this->saleRepository    = $saleRepository;
    }

    /** @param string[] $options */
    public function sale(Service $service, int $numberOfPayments = 1, bool $withInterest = false, array $options = []): Gateway
    {
        $customer    = Customer::fill($service->customer);
        $transaction = new Transaction();
        $price       = $withInterest
            ? Interest::apply($service->price, $numberOfPayments, true)
            : $service->price;

        $transaction
            ->defaultOrder(MoneyHelper::realToCents($price))
            ->defaultPayment($numberOfPayments)
            ->setPaymentTokenCard($this->getTokenCard($service))
            ->setCustomer($customer);

        $transaction->getPayment()->setSoftDescriptor($service->operator);

        if (array_key_exists('urlReturn', $options)) {
            $transaction->setUrlReturn(data_get($options, 'urlReturn', ''));
        }

        $response = $this->gatewayConnection->sale($transaction);

        $this->saleRepository->updateService($service, [
            'price'   => $price,
            'payment' => [
                'gatewayReference'     => $transaction->getOrder()->getReference(),
                'gatewayTransactionId' => $response->getTransactionID(),
                'gatewayStatus'        => $response->getStatus(),
                'date'                 => now()->toIso8601String(),
                'times'                => $numberOfPayments,
                'interest'             => $withInterest ? $price - Interest::remove($price, $numberOfPayments) : 0.0
            ]
        ]);

        if ($response->isAuthorized()) {
            $this->updatePaymentStatus($service, ServiceStatus::APPROVED);
            return $response;
        }

        throw GatewayExceptions::gatewayTransactionNotApproved();
    }

    public function cancel(Service $service, $extra = null): Gateway
    {
        $response = $this->gatewayConnection->cancel($this->getTransactionId($service), $extra);

        if ($response->getStatus() === GatewayStatus::CANCELLED) {
            $this->updatePaymentStatus($service, ServiceStatus::CANCELED);
            return $response;
        }

        throw GatewayExceptions::gatewayErrorCancelingTheSale();
    }

    public function authorize(Service $service, int $amount = 100, int $numPayments = 1): Gateway
    {
        $customer    = Customer::fill($service->customer);
        $transaction = new Transaction();

        $transaction
            ->defaultOrder($amount)
            ->defaultPayment($numPayments)
            ->setPaymentTokenCard($this->getTokenCard($service))
            ->setCustomer($customer);

        $transaction->getPayment()->setSoftDescriptor($service->operator);

        $response = $this->gatewayConnection->authorize($transaction);

        if ($response->isAuthorized()) {
            $this->gatewayConnection->cancel($response->getTransactionID());
            return $response;
        }

        $this->saleRepository->pushLogService($service, [
            'card' => [
                'action'  => GatewayMethodsEnum::AUTHORIZE,
                'status'  => GatewayStatus::UNAUTHORIZED,
                'message' => trans('gateway::exceptions.' . GatewayExceptions::CARD_UNAUTHORIZED),
                'date'    => MongoDateHelper::dateTimeToUtc(now())
            ]
        ]);

        throw GatewayExceptions::cardUnauthorized();
    }

    public function tokenize(Service $service, array $cardArray): Service
    {
        $customer   = Customer::fill($service->customer);
        $creditCard = CreditCard::fill($cardArray);

        $transaction = (new Transaction())
            ->setCustomer($customer)
            ->setPaymentCard($creditCard);

        $token = $this->gatewayConnection->tokenize($transaction)->getTokenCard();

        if (is_string($token)) {
            return $this->saveToken($service, $token);
        }

        throw GatewayExceptions::tokenCardInvalid();
    }

    private function updatePaymentStatus(Service $service, string $status): Service
    {
        $payments           = $service->payment ?? [];
        $payments['status'] = $status;

        return $this->saleRepository->updateService($service, [
            'payment' => $payments
        ]);
    }

    private function saveToken(Service $service, string $token): Service
    {
        $this->saleRepository->updateService($service, [
            'card' => [
                'token' => $token,
                'date' => MongoDateHelper::dateTimeToUtc(now())
            ]
        ]);

        return $service;
    }

    public function getTokenCard(Service $service): string
    {
        $token = $service->getTokenCard();

        if ($token === null) {
            throw ServiceExceptions::tokenCardNotFound();
        }

        return $token;
    }

    public function getTransactionId(Service $service): string
    {
        $transactionId = $service->getGatewayTransaction();

        if ($transactionId === null) {
            throw GatewayExceptions::transactionIdNotFound();
        }

        return $transactionId;
    }
}

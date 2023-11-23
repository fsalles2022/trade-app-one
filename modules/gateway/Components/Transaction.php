<?php

namespace Gateway\Components;

use Gateway\API\Acquirers;
use Gateway\API\Currency;
use Gateway\API\Transaction as TransactionSDK;
use TradeAppOne\Facades\Uniqid;

class Transaction extends TransactionSDK
{
    /**
     * Payment require 'amount' of the 'Order'
     * Build Sequence:
     * - Order
     * - Payment
     *
     * Or define setAmount on Payment
     */

    public const COUNTRY = 'BRA';

    public function defaultOrder(int $amount = 100): Transaction
    {
        $saleReference = Uniqid::generate();

        if ($this->getOrder() === null) {
            $this->Order();
        }

        $this->getOrder()
            ->setReference($saleReference)
            ->setTotalAmount($amount);

        return $this;
    }

    public function defaultPayment(int $numberPayments = 1): Transaction
    {
        if ($this->getPayment() === null) {
            $this->Payment();
        }

        $this->getPayment()
            ->setAcquirer(Acquirers::TRADE_UP)
            ->setMethodBasedInPayments($numberPayments)
            ->setCurrency(Currency::BRAZIL_BRAZILIAN_REAL_BRL)
            ->setCountry(self::COUNTRY)
            ->setNumberOfPayments($numberPayments);

        return $this;
    }

    public function setPaymentCard(CreditCard $creditCard): Transaction
    {
        if ($this->getPayment() === null) {
            $this->Payment();
        }

        $creditCard->softDescriptor
            ? $this->getPayment()->setSoftDescriptor($creditCard->softDescriptor)->Card($creditCard->toArray())
            : $this->getPayment()->Card($creditCard->toArray());

        return $this;
    }

    public function setPaymentTokenCard(string $token): Transaction
    {
        if ($this->getPayment() === null) {
            $this->Payment();
        }

        $this->getPayment()
            ->setTokenCard($token)
            ->Card()
            ->setTokenCard($token);

        return $this;
    }

    public function setCustomer(Customer $customer): Transaction
    {
        $this->Customer($customer->toArray());
        return $this;
    }
}

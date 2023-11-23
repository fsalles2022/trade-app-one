<?php

namespace TradeAppOne\Domain\Factories;

use Buyback\Services\TradeInCancelService;
use Generali\Services\GeneraliCancelService;
use McAfee\Services\McAfeeCancelService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\Cancel\ServiceCancel;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use Uol\Services\UolCancelService;

class CancelFactory
{
    public const CANCEL_OPERATORS = [
        Operations::UOL             => UolCancelService::class,
        Operations::MCAFEE          => McAfeeCancelService::class,
        Operations::TRADE_IN_MOBILE => TradeInCancelService::class,
        Operations::GENERALI        => GeneraliCancelService::class
    ];

    public static function make(string $operator): ServiceCancel
    {
        try {
            return resolve(self::CANCEL_OPERATORS[$operator]);
        } catch (\Exception $exception) {
            throw ServiceExceptions::cannotBeCancel();
        }
    }
}

<?php

declare(strict_types=1);

namespace SurfPernambucanas\Assistances;

use SurfPernambucanas\Connection\PagtelConnection;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\AssistanceBehavior;

/** Represent factory of objects for operations Pernambucanas */
class SurfPernambucanasAssistanceFactory
{
    /** @var string[] */
    protected static $operations = [
        Operations::SURF_PERNAMBUCANAS_PRE              => SurfPernambucanasPreAssistance::class,
        Operations::SURF_PERNAMBUCANAS_PRE_RECHARGE     => SurfPernambucanasRechargeAssistance::class,
        Operations::SURF_PERNAMBUCANAS_SMART_CONTROL    => SurfPernambucanasSmartControlAssistance::class,
        Operations::SURF_CORREIOS_PRE                   => SurfPernambucanasPreAssistance::class,
        Operations::SURF_CORREIOS_PRE_RECHARGE          => SurfPernambucanasRechargeAssistance::class,
        Operations::SURF_CORREIOS_SMART_CONTROL         => SurfPernambucanasSmartControlAssistance::class,
    ];

    public static function make(string $operation): AssistanceBehavior
    {
        return app()->makeWith(self::$operations[$operation], ['pagtelService' => self::getService($operation)]);
    }

    private static function getService(?string $operation = null): PagtelService
    {
        return app()->make(PagtelService::class, [
            'client' => self::getClientIdByOperation($operation),
        ]);
    }

    /** Necessary to change client connection by Pagtel */
    private static function getClientIdByOperation(string $operation): string
    {
        if (in_array($operation, array_keys(Operations::TELECOMMUNICATION_OPERATORS[Operations::SURF_CORREIOS]))) {
            return PagtelConnection::PAGTEL_CORREIOS;
        }

        return PagtelConnection::PAGTEL_PERNAMBUCANAS;
    }
}

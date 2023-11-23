<?php

declare(strict_types=1);

namespace ClaroBR\OperationAssistances;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class ClaroAssistants
{
    private static $ASSISTANTS = [
        Operations::CLARO_POS             => ClaroPosAssistance::class,
        Operations::CLARO_CONTROLE_BOLETO => ClaroControleBoletoAssistant::class,
        Operations::CLARO_PRE             => ClaroPreAssistant::class,
        Operations::CLARO_CONTROLE_FACIL  => [
            'v2' => ClaroControleFacilAssistant::class, // This is activated by SIV Legacy
            'v3' => ClaroControleFacilV3Assistant::class, // This is activated by TradeHUB
        ],
        Operations::CLARO_BANDA_LARGA     => ClaroBandaLargaAssistant::class,
    ];

    public static function make(Service $service): ClaroAssistantBehavior
    {
        $strategy = $service->operation;
        $assistant = self::$ASSISTANTS[$strategy] ?? null;

        if ($strategy === Operations::CLARO_CONTROLE_FACIL) {
            if ($service->tradeHub !== null) {
                return resolve($assistant['v3']);
            }

            return resolve($assistant['v2']);
        }

        return resolve($assistant);
    }
}

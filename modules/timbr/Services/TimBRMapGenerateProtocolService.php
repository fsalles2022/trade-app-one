<?php

declare(strict_types=1);

namespace TimBR\Services;

use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;

class TimBRMapGenerateProtocolService
{
    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    public static function map(array $service): array
    {
        return [
            'interaction' => [
                'msisdn' => $service['msisdn'] ?? null,
                'documentNumber' => $service['customer']['cpf'] ?? null,
                'flagSms' => "SIM",
                'directionContact' => "FROM-CLIENT",
                'source' => "APPVAREJO",
                'type' => "Web",
                'status' => "OPENED",
                'requestFlag' => "false",
                'reason1' => "Solicitação",
                'reason2' => self::getReasonTwo($service),
                'reason3' => self::getReasonThree($service)
            ]
        ];
    }

    private static function getReasonTwo(array $service): string
    {
        $operation = $service['operation'] ?? null;
        $mode      = $service['mode'] ?? null;

        if ($operation === Operations::TIM_BLACK_MULTI) {
            return 'Digital';
        }

        if (in_array($mode, [ Modes::ACTIVATION, Modes::PORTABILITY ])) {
            return 'Contrato';
        }

        return 'Migração';
    }

    private static function getReasonThree(array $service): string
    {
        $operation = $service['operation'] ?? null;
        $mode      = $service['mode'] ?? null;

        if ($operation === Operations::TIM_PRE_PAGO) {
            return 'Realizado';
        }

        if (in_array($operation, [ Operations::TIM_CONTROLE_FATURA, Operations::TIM_CONTROLE_FLEX ]) && $mode === Modes::MIGRATION) {
            return 'Pré/Controle';
        }

        if (in_array($operation, [ Operations::TIM_BLACK, Operations::TIM_BLACK_EXPRESS ]) && $mode === Modes::MIGRATION) {
            return 'Pré p/ Pós';
        }

        return 'Ativacao de Linha';
    }
}

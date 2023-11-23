<?php

namespace TimBR\Adapters;

use Illuminate\Support\Str;
use stdClass;
use ErrorException;
use TimBR\Enumerators\TimBRSegments;
use TimBR\Exceptions\TimBREligibilityException;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;

class TimBRElegibilityRequestAdapterV3
{
    public static function adapt($fields = []): array
    {
        try {
            $customer     = $fields['customer'];
            $date         = date('d/m/Y', strtotime($customer['birthday']));
            $portedNumber = data_get($fields, 'portedNumber', null);

            if (Str::contains($fields['operation'], Operations::TIM_CONTROLE_FATURA)) {
                $segment = TimBRSegments::CONTROLE;
            }

            if (Str::contains($fields['operation'], 'EXPRESS')) {
                $segment = 'EXPRESS';
            }

            if (Str::contains($fields['operation'], 'PRE_PAGO')) {
                $segment = TimBRSegments::PRE_PAGO;
            }

            if (Str::contains($fields['operation'], Operations::TIM_CONTROLE_FLEX)) {
                $segment = TimBRSegments::CONTROLE_FLEX;
            }

            if (Str::contains($fields['operation'], Operations::TIM_BLACK)) {
                $segment = TimBRSegments::POS_PAGO;
            }

            if (Str::contains($fields['operation'], Operations::TIM_BLACK_EXPRESS)) {
                $segment = TimBRSegments::POS_EXPRESS;
            }

            // Tim Black Multi And Tim Black Multi Dependent
            if (Str::contains($fields['operation'], Operations::TIM_BLACK_MULTI)) {
                $segment = TimBRSegments::DIGITALPOS;
            }

            if ($portedNumber !== null) {
                $areaCode = MsisdnHelper::getAreaCode($portedNumber);
            } else {
                $areaCode = data_get($fields, 'areaCode', null)
                    ?? MsisdnHelper::getAreaCode(data_get($fields, 'msisdn', ''));
            }

            return self::createPayload($fields, $customer, $date, $segment, $areaCode);
        } catch (ErrorException $exception) {
            throw new TimBREligibilityException($exception->getMessage());
        }
    }

    protected static function createPayload(array $fields, array $customer, ?string $date, string $segment, ?string $areaCode): array
    {
        $mode       = $fields['mode'] ?? '';
        $operation  = $fields['operation'] ?? '';

        $payload = [
            'pdv'         => [
                'custCode'  => $fields['pointOfSale'],
                'state' => $fields['state'],
            ],
            'customer'    => [
                'socialSecNo' => (string) $customer['cpf'],
                'name'        => "{$customer['firstName']} {$customer['lastName']}",
                'filiation'  => $customer['filiation'],
                'birthDate'   => $date,
                'type' => 'CPF',

                'address' => array_filter([
                    'zipCode' => (string) $customer['zipCode'],
                ]),

                'takeOver' => [
                    'socialSecNo' => ''
                ],
            ],
            'smartThreadWorking' => false,
            'actionType' => $mode === Modes::MIGRATION ? "MIGRACAO" : "GROSS",
            'eligibilityToken' => $fields['transactionToken'],
            'plan'        => [
                'segment' => $segment,
            ],
            'newContract' => [
                'ddd' => $areaCode,
                'msisdnMaster' => $operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? $fields['masterNumber'] ?? '' : '',
                'groupMemberType' => $operation === Operations::TIM_BLACK_MULTI_DEPENDENT ? 'FAMILIA DEPENDENTE' : '',
            ],
            'contract' => array_filter([
                'msisdn' => $fields['msisdn'] ?? ''
            ]),
        ];

        if (empty(data_get($payload, 'contract'))) {
            unset($payload['contract']);
        }

        return $payload;
    }
}

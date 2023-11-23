<?php

namespace TimBR\Adapters;

use Illuminate\Support\Str;
use TimBR\Enumerators\TimBRSegments;
use TimBR\Exceptions\TimBREligibilityException;
use TradeAppOne\Domain\Enumerators\Operations;

class TimBRElegibilityRequestAdapter
{

    public static function adapt($fields = [])
    {
        try {
            $customer = $fields['customer'];
            $date     = date('d/m/Y', strtotime($customer['birthday']));
            if (Str::contains($fields['operation'], 'CONTROLE')) {
                $segment = 'CONTROLE';
            }

            if (Str::contains($fields['operation'], 'EXPRESS')) {
                $segment = 'EXPRESS';
            }

            if (Str::contains($fields['operation'], 'PRE_PAGO')) {
                $segment = 'PRE_PAGO';
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

            if (Str::contains($fields['operation'], Operations::TIM_BLACK_MULTI)) {
                $segment = TimBRSegments::DIGITALPOS;
            }

            return array_filter([
                'pdv'      => [
                    'custCode'  => $fields['pointOfSale'],
                    'stateCode' => $fields['state'],
                ],
                'customer' => [
                    'socialSecNo' => (string) $customer['cpf'],
                    'name'        => "{$customer['firstName']} {$customer['lastName']}",
                    'motherName'  => $customer['filiation'],
                    'birthDate'   => $date,

                    'address' => array_filter([
                        'postalCode' => (string) $customer['zipCode'],
                    ])
                ],
                'contract' => array_filter([
                    'msisdn' => $fields['msisdn'] ?? ''
                ]),
                'plan'     => [
                    'segment' => $segment,
                ]
            ]);
        } catch (\ErrorException $exception) {
            throw new TimBREligibilityException($exception->getMessage());
        }
    }
}

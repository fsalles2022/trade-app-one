<?php

namespace Buyback\Exportables\Sales;

use Buyback\Exportables\AnalyticalReportIndexes as Index;
use Illuminate\Support\Arr;
use MongoDB\BSON\UTCDateTime;
use TradeAppOne\Domain\Components\Elasticsearch\DatetimeConverter;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class BuybackMapSale
{
    private const STATUS_DEVICE_IN_STORE    = 'EM LOJA';
    private const STATUS_DEVICE_ON_CARRIAGE = 'EM TRÂNSITO';
    private const STATUS_DEVICE_APPRAISED   = 'AVALIADO PELO TÉCNICO';

    private const DEVICE_STATUS = [
        ServiceStatus::ACCEPTED => [
            0 => self::STATUS_DEVICE_IN_STORE,
            1 => self::STATUS_DEVICE_ON_CARRIAGE
        ],
        ServiceStatus::APPROVED => [
            0 => '-',
            1 => self::STATUS_DEVICE_APPRAISED
        ]
    ];

    public static function recordsToArray(array $sales): array
    {
        $toExport    = [];
        $toExport[]  = Index::headings();
        $rows        = self::collection($sales);
        $sortedTable = [];

        foreach ($rows as $row) {
            $sortedRow = [];
            foreach (Index::headings() as $head) {
                $sortedRow[$head] = $row[$head];
            }
            $sortedTable[] = $sortedRow;
        }
        return array_merge($toExport, $sortedTable);
    }

    private static function collection(array $sales): array
    {
        $salesToExport = [];

        foreach ($sales as $sale) {
            $sale = data_get($sale, '_source');

            $export                                       = [];
            $firstName                                    = data_get($sale, 'service_customer_firstname');
            $priceSalesman                                = data_get($sale, 'service_evaluations_salesman_price', 0);
            $priceAppraiser                               = data_get($sale, 'service_evaluations_appraiser_price', 0) ?? 0;
            $priceCarrier                                 = data_get($sale, 'service_evaluations_carrier_price', 0) ?? 0;
            $serviceStatus                                = data_get($sale, 'service_status');
            $waybillId                                    = data_get($sale, 'service_waybill_id');
            $export[Index::DATE]                          = DatetimeConverter::splitDateAndTime(data_get($sale, 'created_at'))[0];
            $export[Index::HOUR]                          = DatetimeConverter::splitDateAndTime(data_get($sale, 'created_at'))[1];
            $export[Index::SALETRANSACTION]               = data_get($sale, 'service_servicetransaction');
            $export[Index::CPF]                           = data_get($sale, 'service_customer_cpf');
            $export[Index::NAME]                          = $firstName . ' ' . data_get($sale, 'service_customer_firstname');
            $export[Index::EMAIL]                         = data_get($sale, 'service_customer_email');
            $export[Index::CITY]                          = data_get($sale, 'service_customer_city');
            $export[Index::ZIPCODE]                       = data_get($sale, 'service_customer_zipcode');
            $export[Index::LOCAL]                         = data_get($sale, 'service_customer_neighborhood');
            $export[Index::NUMBER]                        = data_get($sale, 'service_customer_number');
            $export[Index::COMPLEMENT]                    = data_get($sale, 'service_customer_complement');
            $export[Index::IMEI]                          = data_get($sale, 'service_imei');
            $export[Index::MODELID]                       = data_get($sale, 'service_device_id');
            $export[Index::MODEL]                         = data_get($sale, 'service_device_model');
            $export[Index::STORAGE]                       = data_get($sale, 'service_device_storage');
            $export[Index::COLOR]                         = data_get($sale, 'service_device_color');
            $export[Index::PRICESALESMAN]                 = $priceSalesman;
            $export[Index::PRICEAPPRAISER]                = $priceAppraiser;
            $export[Index::DATEAPPRAISER]                 = data_get($sale, 'service_evaluations_appraiser_created_at');
            $export[Index::PRICECARRIER]                  = $priceCarrier;
            $export[Index::DIFF]                          = ($priceSalesman - $priceAppraiser);
            $export[Index::CNPJ]                          = data_get($sale, 'pointofsale_cnpj');
            $export[Index::POINTOFSALE_SLUG]              = data_get($sale, 'pointofsale_slug');
            $export[Index::PDV_CITY]                      = data_get($sale, 'pointofsale_city');
            $export[Index::PDV_LOCAL]                     = data_get($sale, 'pointofsale_local');
            $export[Index::PDV_ZIPCODE]                   = data_get($sale, 'pointofsale_zipcode');
            $export[Index::PDV_NETWORK]                   = data_get($sale, 'pointofsale_network_label');
            $export[Index::NETWORK_OPERATION]             = data_get($sale, 'service_operation');
            $export[Index::PDV_NUMBER]                    = data_get($sale, 'pointofsale_number');
            $export[Index::RECEIVED_AT]                   = data_get($sale, 'service_received_at');
            $export[Index::STATUS]                        = $serviceStatus === '-' ? '-' : trans("status.{$serviceStatus}");
            $export[Index::STATUS_DEVICE]                 = self::getStatusDevice($serviceStatus, $waybillId);
            $export[Index::PRICE]                         = data_get($sale, 'service_price');
            $export[Index::WAYBILL_ID]                    = data_get($sale, 'service_waybill_id');
            $export[Index::WAYBILL_DATE]                  = self::formatDateUTCDateTime(data_get($sale, 'service_waybill_printed_at', ''));
            $export[Index::WAYBILL_WITHDRAWN]             = data_get($sale, 'service_waybill_withdrawn', '-');
            $export[Index::WAYBILL]                       = data_get($sale, 'service_waybill_printedat');
            $export[Index::EVALUATIONS_BONUS_SPONSORS]    = self::mountEvaluationsBonusSponsors($sale);
            $export[Index::EVALUATIONS_BONUS_VALUES]      = self::mountEvaluationsBonusValues($sale);
            $export[Index::SALESMAN_NAME]                 = self::mountUserFullName($sale);
            $export[Index::HAS_RECOMMENDATION]            = data_get($sale, 'service_hasrecommendation', false) ?
                'Sim' : 'Não';
            $export[Index::RECOMMENDATION_REGISTRATION]   = data_get($sale, 'service_recommendation_registration');
            $export[Index::QUESTIONS_ANSWERS_SALESMAN]    = self::mountQuestionAnswer($sale, Index::QUESTIONS_ANSWERS_SALESMAN);
            $export[Index::QUESTIONS_ANSWERS_TECHNICAL]   = self::mountQuestionAnswer($sale, Index::QUESTIONS_ANSWERS_TECHNICAL);
            $export[Index::PPRAISER_TECHNICIAN_EVALUATOR] = data_get($sale, 'service_evaluations_appraiser_user_firstName');


            $salesToExport[] = $export;
        }
        return $salesToExport;
    }

    public static function getStatusDevice($serviceStatus, $waybillId = null): string
    {
        if (! in_array($serviceStatus, [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED], true)) {
            return '-';
        }

        return self::DEVICE_STATUS[$serviceStatus][$waybillId === null ? 0 : 1];
    }

    private static function mountEvaluationsBonusSponsors(array $sale): string
    {
        $sponsors = '';
        $index    = 0;
        while ($index <= 5) {
            $key     = 'service_evaluationsbonus_'.$index.'_sponsor';
            $sponsor = data_get($sale, $key, null);
            if (null !== $sponsor) {
                $sponsors .= $sponsor . '; ';
                $index++;
                continue;
            }
            break;
        }
        return trim($sponsors);
    }

    private static function mountEvaluationsBonusValues(array $sale): string
    {
        $values = '';
        $index  = 0;
        while ($index <= 5) {
            $key   = 'service_evaluationsbonus_'.$index.'_bonusvalue';
            $value = data_get($sale, $key, null);
            if (null !== $value) {
                $values .= MoneyHelper::formatMoney($value) . '; ';
                $index++;
                continue;
            }
            break;
        }
        return trim($values);
    }

    private static function mountUserFullName(array $sale): string
    {
        $firstName = data_get($sale, 'user_firstname', '');
        $lastName  = data_get($sale, 'user_lastname', '');
        return $firstName . ' ' . $lastName;
    }

    public static function mountQuestionAnswer(array $sale, string $type): string
    {
        $exist              = true;
        $index              = 0;
        $questionsAndAnswer = '';

        while ($exist) {
            $answeredBy = ($type === Index::QUESTIONS_ANSWERS_SALESMAN) ? 'salesman' : 'appraiser';

            $keyQuestion = "service_evaluations_{$answeredBy}_questions_{$index}_question";
            $keyAnswer   = "service_evaluations_{$answeredBy}_questions_{$index}_answer";

            $valueQuestion = Arr::get($sale, $keyQuestion);
            $valueAnswer   = Arr::get($sale, $keyAnswer, '- ;') ;

            if ($valueQuestion === null) {
                $exist = false;
                break;
            }

            if ($valueAnswer !== '- ;') {
                $valueAnswer = $valueAnswer ? 'Sim;' : 'Não;';
            }

            $index++;
            $questionsAndAnswer .= "Pergunta {$index}: {$valueQuestion} {$valueAnswer} ";
        }

        return trim($questionsAndAnswer);
    }

    public static function formatDateUTCDateTime(?object $date): string
    {
        // @var UTCDateTime
        if ($date instanceof UTCDateTime === false) {
            return '';
        }

        return $date->toDateTime()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('d/m/Y H:i:s');
    }
}

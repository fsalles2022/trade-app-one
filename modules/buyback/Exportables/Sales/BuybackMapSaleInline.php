<?php

declare(strict_types=1);

namespace Buyback\Exportables\Sales;

use Buyback\Exportables\AnalyticalReportIndexes;
use Illuminate\Support\Arr;
use Reports\AnalyticalsReports\Input\SalesCollectionMappableInterface;
use TradeAppOne\Domain\Components\Elasticsearch\DatetimeConverter;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use MongoDB\BSON\UTCDateTime;

class BuybackMapSaleInline
{
    private const STATUS_DEVICE_IN_STORE    = 'EM LOJA';
    private const STATUS_DEVICE_ON_CARRIAGE = 'EM TRÂNSITO';
    private const STATUS_DEVICE_APPRAISED   = 'AVALIADO PELO TÉCNICO';

    /** @var array[] */
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

    /** @return mixed[] */
    public static function recordsToArray(SalesCollectionMappableInterface $sales): array
    {
        $toExport = [];
        $rows     = self::collection($sales->toArray());

        $toExport[]  = AnalyticalReportIndexes::headings();
        $sortedTable = [];

        foreach ($rows as $row) {
            $sortedRow = [];
            foreach (AnalyticalReportIndexes::headings() as $head) {
                $sortedRow[$head] = $row[$head];
            }
            $sortedTable[] = $sortedRow;
        }

        return array_merge($toExport, $sortedTable);
    }

    /**
     * @param mixed[] $sales
     * @return mixed[]
     */
    public static function collection(array $sales): array
    {
        $rows = [];
        foreach ($sales as $row) {
            $rows[] = self::body($row);
        }

        return $rows;
    }

    /**
     * @param mixed[] $row
     * @return mixed[]
     */
    public static function body(array $row): array
    {
        $waybill       = data_get($row, 'service_waybill_id');
        $serviceStatus = data_get($row, 'service_status');

        $bonus = data_get($row, 'service_evaluations_bonus', []);

        $evaluationAppraiserFullName = data_get($row, 'service_evaluations_appraiser_user_first_name') . ' ' . data_get($row, 'service_evaluations_appraiser_user_last_name');

        return [
            AnalyticalReportIndexes::DATE => DatetimeConverter::splitDateAndTime((string) data_get($row, 'created_at'))[0],
            AnalyticalReportIndexes::HOUR => DatetimeConverter::splitDateAndTime((string) data_get($row, 'created_at'))[1],
            AnalyticalReportIndexes::SALETRANSACTION => data_get($row, 'service_servicetransaction'),
            AnalyticalReportIndexes::CPF => data_get($row, 'service_customer_cpf'),
            AnalyticalReportIndexes::NAME => self::getFullNameClient($row),
            AnalyticalReportIndexes::EMAIL => data_get($row, 'service_customer_email'),
            AnalyticalReportIndexes::CITY => data_get($row, 'service_customer_city'),
            AnalyticalReportIndexes::LOCAL => data_get($row, 'service_customer_local'),
            AnalyticalReportIndexes::ZIPCODE => data_get($row, 'service_customer_zipcode'),
            AnalyticalReportIndexes::NUMBER => data_get($row, 'service_customer_number'),
            AnalyticalReportIndexes::COMPLEMENT => data_get($row, 'service_customer_complement'),
            AnalyticalReportIndexes::IMEI => data_get($row, 'service_imei'),
            AnalyticalReportIndexes::MODELID => data_get($row, 'service_device_id'),
            AnalyticalReportIndexes::MODEL => data_get($row, 'service_device_model'),
            AnalyticalReportIndexes::STORAGE => data_get($row, 'service_device_storage'),
            AnalyticalReportIndexes::COLOR => data_get($row, 'service_device_color'),
            AnalyticalReportIndexes::PRICESALESMAN => data_get($row, 'service_evaluations_salesman_price'),
            AnalyticalReportIndexes::PRICEAPPRAISER => data_get($row, 'service_evaluations_appraiser_price'),
            AnalyticalReportIndexes::PRICECARRIER => data_get($row, 'service_evaluations_carrier_price'),
            AnalyticalReportIndexes::DIFF => data_get($row, 'service_evaluations_salesman_appraiser_diff'),
            AnalyticalReportIndexes::CNPJ => data_get($row, 'pointofsale_cnpj'),
            AnalyticalReportIndexes::POINTOFSALE_SLUG => data_get($row, 'pointofsale_slug'),
            AnalyticalReportIndexes::PDV_CITY => data_get($row, 'pointofsale_city'),
            AnalyticalReportIndexes::PDV_LOCAL => data_get($row, 'pointofsale_local'),
            AnalyticalReportIndexes::PDV_NUMBER => data_get($row, 'pointofsale_number'),
            AnalyticalReportIndexes::PDV_ZIPCODE => data_get($row, 'pointofsale_zipcode'),
            AnalyticalReportIndexes::PDV_NETWORK => data_get($row, 'pointofsale_network_label'),
            AnalyticalReportIndexes::NETWORK_OPERATION => data_get($row, 'service_operation'),
            AnalyticalReportIndexes::RECEIVED_AT => '',
            AnalyticalReportIndexes::PRICE => data_get($row, 'service_price'),
            AnalyticalReportIndexes::STATUS => $serviceStatus === '-' ? '-' : trans("status.{$serviceStatus}"),
            AnalyticalReportIndexes::STATUS_DEVICE => self::getStatusDevice($serviceStatus, (int) $waybill),
            AnalyticalReportIndexes::WAYBILL_ID => (string) $waybill,
            AnalyticalReportIndexes::WAYBILL_DATE => self::formatDateUTCDateTime(data_get($row, 'service_waybill_printed_at', '')),
            AnalyticalReportIndexes::WAYBILL_WITHDRAWN => data_get($row, 'service_waybill_withdrawn'),
            AnalyticalReportIndexes::WAYBILL => data_get($row, 'service_waybill_printed_at'),
            AnalyticalReportIndexes::EVALUATIONS_BONUS_SPONSORS => self::getEvaluationsBonusSponsor($bonus),
            AnalyticalReportIndexes::EVALUATIONS_BONUS_VALUES => self::getEvaluationsBonusValue($bonus),
            AnalyticalReportIndexes::SALESMAN_NAME => self::getFullNameUser($row),
            AnalyticalReportIndexes::HAS_RECOMMENDATION => data_get($row, 'service_hasrecommendation', false) ? 'Sim' : 'Não',
            AnalyticalReportIndexes::RECOMMENDATION_REGISTRATION => data_get($row, 'service_recommendation_registration'),
            AnalyticalReportIndexes::QUESTIONS_ANSWERS_SALESMAN => self::getQuestionsAndAnswers(data_get($row, 'service_evaluations_salesman_questions')),
            AnalyticalReportIndexes::QUESTIONS_ANSWERS_TECHNICAL => self::getQuestionsAndAnswers(data_get($row, 'service_evaluations_appraiser_questions')),
            AnalyticalReportIndexes::PPRAISER_TECHNICIAN_EVALUATOR => $evaluationAppraiserFullName,
            AnalyticalReportIndexes::DATEAPPRAISER => self::formatDateUTCDateTime(data_get($row, 'service_evaluations_appraiser_created_at', '')),

        ];
    }

    /** @param string[] $row */
    public static function getFullNameClient(array $row): string
    {
        return data_get($row, 'service_customer_firstname', '') . ' ' .
                data_get($row, 'service_customer_lastname', '');
    }

    /** @param string[] $row */
    public static function getFullNameUser(array $row): string
    {
        return data_get($row, 'user_firstname', '') . ' ' .
                data_get($row, 'user_lastname', '');
    }

    public static function getStatusDevice(string $serviceStatus, ?int $waybillId = null): string
    {
        if (! in_array($serviceStatus, [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED], true)) {
            return '-';
        }

        return self::DEVICE_STATUS[$serviceStatus][$waybillId === null ? 0 : 1];
    }

    /** @param array[] $questions */
    public static function getQuestionsAndAnswers(array $questions): string
    {
        $questionsFormatted = '';

        foreach ($questions as $index => $question) {
            $valueQuestion = Arr::get($question, 'question');
            $valueAnswer   = Arr::get($question, 'answer', '- ;') ;

            if ($valueAnswer !== '- ;') {
                $valueAnswer = $valueAnswer ? 'Sim;' : 'Não;';
            }

            $questionsFormatted .= "Pergunta {$index}: {$valueQuestion} {$valueAnswer} ";
        }
        return $questionsFormatted;
    }

    /** @param array[] $bonus */
    public static function getEvaluationsBonusValue(array $bonus): string
    {
        return (string) data_get($bonus, '0.bonusValue', 0);
    }

    /** @param array[] $bonus */
    public static function getEvaluationsBonusSponsor(array $bonus): string
    {
        return data_get($bonus, '0.sponsor', '');
    }

    /**
     * @param object|null $date
     * @return string
     */
    public static function formatDateUTCDateTime(?object $date): string
    {
        // @var UTCDateTime
        if ($date instanceof UTCDateTime === false) {
            return '';
        }

        return $date->toDateTime()->setTimezone(new \DateTimeZone('America/Sao_Paulo'))->format('d/m/Y H:i:s');
    }
}

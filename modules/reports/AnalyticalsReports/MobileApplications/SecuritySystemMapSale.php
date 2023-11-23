<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\MobileApplications;

use Carbon\Carbon;
use Reports\AnalyticalsReports\Input\SalesCollectionMappableInterface;

class SecuritySystemMapSale
{
    public static function recordsToArray(SalesCollectionMappableInterface $sales): array
    {
        $toExport = [];
        array_push($toExport, self::headings());
        $rows        = self::collection($sales->toArray());
        $sortedTable = [];
        foreach ($rows as $row) {
            $sortedRow = [];
            foreach (self::headings() as $head) {
                $sortedRow[$head] = $row[$head];
            }
            array_push($sortedTable, $sortedRow);
        }
        return array_merge($toExport, $sortedTable);
    }

    public static function headings(): array
    {
        return [
            SecuritySystemsHeaderEnum::SERVICE_SECTOR,
            SecuritySystemsHeaderEnum::SERVICE_OPERATOR,
            SecuritySystemsHeaderEnum::SERVICE_SERVICE_ID,
            SecuritySystemsHeaderEnum::SERVICE_LABEL,
            SecuritySystemsHeaderEnum::SERVICE_PRICE,
            SecuritySystemsHeaderEnum::PAYMENT_TIMES,
            SecuritySystemsHeaderEnum::SERVICE_OPERATION,
            SecuritySystemsHeaderEnum::SERVICE_LOG_STATUS,
            SecuritySystemsHeaderEnum::POINTOFSALE_HIERARCHY_LABEL,
            SecuritySystemsHeaderEnum::CREATED_AT_DATE,
            SecuritySystemsHeaderEnum::CREATED_AT_HOUR,
            SecuritySystemsHeaderEnum::SERVICE_IMEI,
            SecuritySystemsHeaderEnum::USER_FULL_NAME,
            SecuritySystemsHeaderEnum::USER_CPF,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_FULL_NAME,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_CPF,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_CITY,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_STATE,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_EMAIL,
            SecuritySystemsHeaderEnum::SERVICE_STATUS,
            SecuritySystemsHeaderEnum::POINTOFSALE_LABEL,
            SecuritySystemsHeaderEnum::POINTOFSALE_CNPJ,
            SecuritySystemsHeaderEnum::POINTOFSALE_STATE,
            SecuritySystemsHeaderEnum::POINTOFSALE_NETWORK_LABEL,
            SecuritySystemsHeaderEnum::POINTOFSALE_NETWORK_SLUG,
            SecuritySystemsHeaderEnum::POINTOFSALE_SLUG,
            SecuritySystemsHeaderEnum::POINTOFSALE_CEP,
            SecuritySystemsHeaderEnum::HAS_RECOMMENDATION,
            SecuritySystemsHeaderEnum::RECOMMENDATION_REGISTRATION,
            SecuritySystemsHeaderEnum::TRANSACTION_ID,
            SecuritySystemsHeaderEnum::LOG_PAYMENT_STATUS
        ];
    }

    private static function collection(array $sales): array
    {
        $salesToExport = [];
        foreach ($sales as $sale) {
            $serviceSector = data_get($sale, 'service_sector');

            $export = [];
            $export = array_merge($export, self::mapCustomer($sale));
            $export = array_merge($export, self::mapPointOfSale($sale));
            $export = array_merge($export, self::mapServiceDates($sale));
            $export = array_merge($export, self::mapSalesman($sale));

            $export[SecuritySystemsHeaderEnum::SERVICE_IMEI]                = data_get($sale, 'service_imei');
            $export[SecuritySystemsHeaderEnum::CHANNEL]                     = data_get($sale, 'channel');
            $export[SecuritySystemsHeaderEnum::SOURCE]                      = data_get($sale, 'source');
            $export[SecuritySystemsHeaderEnum::SERVICE_SECTOR]              = trans("operations.{$serviceSector}");
            $export[SecuritySystemsHeaderEnum::SERVICE_OPERATOR]            = data_get($sale, 'service_operator');
            $export[SecuritySystemsHeaderEnum::SERVICE_OPERATION]           = data_get($sale, 'service_operation');
            $export[SecuritySystemsHeaderEnum::SERVICE_SERVICE_ID]          = data_get($sale, 'service_servicetransaction');
            $export[SecuritySystemsHeaderEnum::SERVICE_LABEL]               = data_get($sale, 'service_label');
            $export[SecuritySystemsHeaderEnum::SERVICE_PRICE]               = data_get($sale, 'service_price');
            $export[SecuritySystemsHeaderEnum::SERVICE_STATUS]              = trans('status.' . data_get($sale, 'service_status'));
            $export[SecuritySystemsHeaderEnum::SERVICE_LOG_STATUS]          = self::mapServiceLog($sale);
            $export[SecuritySystemsHeaderEnum::PAYMENT_TIMES]               = data_get($sale, 'service_payment_times');
            $export[SecuritySystemsHeaderEnum::HAS_RECOMMENDATION]          = data_get($sale, 'service_hasrecommendation', false) ?
                'Sim' : 'Não';
            $export[SecuritySystemsHeaderEnum::RECOMMENDATION_REGISTRATION] = data_get($sale, 'service_recommendation_registration');
            $export[SecuritySystemsHeaderEnum::TRANSACTION_ID]              = data_get($sale, 'service_payment_gatewaytransactionid');
            $export[SecuritySystemsHeaderEnum::LOG_PAYMENT_STATUS]          = data_get($sale, 'service_payment_log', '');

            $salesToExport[] = $export;
        }
        return $salesToExport;
    }
    private static function mapCustomer($sale): array
    {
        $firstname = data_get($sale, 'service_customer_firstname');
        $lastname  = data_get($sale, 'service_customer_lastname');

        return [
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_FULL_NAME => $firstname . ' ' . $lastname,
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_CPF       => data_get($sale, 'service_customer_cpf'),
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_CITY      => data_get($sale, 'service_customer_city'),
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_STATE     => data_get($sale, 'service_customer_state'),
            SecuritySystemsHeaderEnum::SERVICE_CUSTOMER_EMAIL     => data_get($sale, 'service_customer_email')
        ];
    }

    private static function mapPointOfSale($sale): array
    {
        return
            [
                SecuritySystemsHeaderEnum::POINTOFSALE_HIERARCHY_LABEL => data_get($sale, 'pointofsale_hierarchy_label', '-'),
                SecuritySystemsHeaderEnum::POINTOFSALE_LABEL           => data_get($sale, 'pointofsale_label'),
                SecuritySystemsHeaderEnum::POINTOFSALE_CNPJ            => data_get($sale, 'pointofsale_cnpj'),
                SecuritySystemsHeaderEnum::POINTOFSALE_CITY            => data_get($sale, 'pointofsale_city'),
                SecuritySystemsHeaderEnum::POINTOFSALE_STATE           => data_get($sale, 'pointofsale_state'),
                SecuritySystemsHeaderEnum::POINTOFSALE_NETWORK_LABEL   => data_get($sale, 'pointofsale_network_label'),
                SecuritySystemsHeaderEnum::POINTOFSALE_NETWORK_SLUG    => data_get($sale, 'pointofsale_network_slug'),
                SecuritySystemsHeaderEnum::POINTOFSALE_SLUG            => data_get($sale, 'pointofsale_slug'),
                SecuritySystemsHeaderEnum::POINTOFSALE_CEP             => data_get($sale, 'pointofsale_zipcode'),
            ];
    }

    private static function mapServiceDates($sale): array
    {
        $carbonInstance     = Carbon::parse(data_get($sale, 'created_at'));
        $carbonInstance->tz = config('app.timezone');
        return [
            SecuritySystemsHeaderEnum::CREATED_AT_DATE => $carbonInstance->format('d/m/Y'),
            SecuritySystemsHeaderEnum::CREATED_AT_HOUR => $carbonInstance->format('H:i')
        ];
    }

    private static function mapSalesman($sale): array
    {
        $firstname = data_get($sale, 'user_firstname');
        $lastname  = data_get($sale, 'user_lastname');

        return [
            SecuritySystemsHeaderEnum::USER_FULL_NAME => $firstname . ' ' . $lastname,
            SecuritySystemsHeaderEnum::USER_CPF       => data_get($sale, 'user_cpf'),
        ];
    }

    private static function mapServiceLog(array $sale): string
    {
        $logs = data_get($sale, 'service_log');

        if (is_null($logs)) {
            return '';
        }

        return data_get(end($logs), 'status', '');
    }
}

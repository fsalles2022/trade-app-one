<?php

namespace Reports\AnalyticalsReports\MobileApplications;

use Carbon\Carbon;

class MobileApplicationMapSale
{
    public static function recordsToArray(array $sales): array
    {
        $toExport = [];
        array_push($toExport, self::headings());
        $rows        = self::collection($sales);
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

    private static function headings(): array
    {
        return [
            MobileApplicationsHeaderEnum::CHANNEL,
            MobileApplicationsHeaderEnum::SOURCE,
            MobileApplicationsHeaderEnum::SERVICE_SECTOR,
            MobileApplicationsHeaderEnum::SERVICE_OPERATOR,
            MobileApplicationsHeaderEnum::SERVICE_SERVICE_ID,
            MobileApplicationsHeaderEnum::SERVICE_LABEL,
            MobileApplicationsHeaderEnum::SERVICE_OPERATION,
            MobileApplicationsHeaderEnum::POINTOFSALE_HIERARCHY_LABEL,
            MobileApplicationsHeaderEnum::CREATED_AT_DATE,
            MobileApplicationsHeaderEnum::CREATED_AT_HOUR,
            MobileApplicationsHeaderEnum::SERVICE_IMEI,
            MobileApplicationsHeaderEnum::USER_FULL_NAME,
            MobileApplicationsHeaderEnum::USER_CPF,
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_FULL_NAME,
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_CPF,
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_CITY,
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_STATE,
            MobileApplicationsHeaderEnum::POINTOFSALE_LABEL,
            MobileApplicationsHeaderEnum::POINTOFSALE_CNPJ,
            MobileApplicationsHeaderEnum::POINTOFSALE_STATE,
            MobileApplicationsHeaderEnum::POINTOFSALE_NETWORK_LABEL,
            MobileApplicationsHeaderEnum::POINTOFSALE_NETWORK_SLUG,
            MobileApplicationsHeaderEnum::POINTOFSALE_SLUG,
        ];
    }

    private static function collection(array $sales): array
    {
        $salesToExport = [];
        foreach ($sales as $sale) {
            $sale          = data_get($sale, '_source');
            $serviceSector = data_get($sale, 'service_sector');

            $export = [];
            $export = array_merge($export, self::mapCustomer($sale));
            $export = array_merge($export, self::mapPointOfSale($sale));
            $export = array_merge($export, self::mapServiceDates($sale));
            $export = array_merge($export, self::mapSalesman($sale));

            $export[MobileApplicationsHeaderEnum::SERVICE_IMEI]       = data_get($sale, 'service_imei');
            $export[MobileApplicationsHeaderEnum::CHANNEL]            = data_get($sale, 'channel');
            $export[MobileApplicationsHeaderEnum::SOURCE]             = data_get($sale, 'source');
            $export[MobileApplicationsHeaderEnum::SERVICE_SECTOR]     = trans("status.{$serviceSector}");
            $export[MobileApplicationsHeaderEnum::SERVICE_OPERATOR]   = data_get($sale, 'service_operator');
            $export[MobileApplicationsHeaderEnum::SERVICE_OPERATION]  = data_get($sale, 'service_operation');
            $export[MobileApplicationsHeaderEnum::SERVICE_SERVICE_ID] = data_get($sale, 'service_servicetransaction');
            $export[MobileApplicationsHeaderEnum::SERVICE_LABEL]      = data_get($sale, 'service_label');

            array_push($salesToExport, $export);
        }
        return $salesToExport;
    }

    private static function mapCustomer($sale): array
    {
        $firstname = data_get($sale, 'service_customer_firstname');
        $lastname  = data_get($sale, 'service_customer_lastname');

        return [
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_FULL_NAME => $firstname . ' ' . $lastname,
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_CPF       => data_get($sale, 'service_customer_cpf'),
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_CITY      => data_get($sale, 'service_customer_city'),
            MobileApplicationsHeaderEnum::SERVICE_CUSTOMER_STATE     => data_get($sale, 'service_customer_state')
        ];
    }

    private static function mapPointOfSale($sale): array
    {
        return
            [
                MobileApplicationsHeaderEnum::POINTOFSALE_HIERARCHY_LABEL => data_get($sale, 'pointofsale_hierarchy_label', '-'),
                MobileApplicationsHeaderEnum::POINTOFSALE_LABEL           => data_get($sale, 'pointofsale_label'),
                MobileApplicationsHeaderEnum::POINTOFSALE_CNPJ            => data_get($sale, 'pointofsale_cnpj'),
                MobileApplicationsHeaderEnum::POINTOFSALE_CITY            => data_get($sale, 'pointofsale_city'),
                MobileApplicationsHeaderEnum::POINTOFSALE_STATE           => data_get($sale, 'pointofsale_state'),
                MobileApplicationsHeaderEnum::POINTOFSALE_NETWORK_LABEL   => data_get($sale, 'pointofsale_network_label'),
                MobileApplicationsHeaderEnum::POINTOFSALE_NETWORK_SLUG    => data_get($sale, 'pointofsale_network_slug'),
                MobileApplicationsHeaderEnum::POINTOFSALE_SLUG            => data_get($sale, 'pointofsale_slug')
            ];
    }

    private static function mapServiceDates($sale): array
    {
        $carbonInstance     = Carbon::parse(data_get($sale, 'created_at'));
        $carbonInstance->tz = config('app.timezone');
        return [
            MobileApplicationsHeaderEnum::CREATED_AT_DATE => $carbonInstance->format('d/m/Y'),
            MobileApplicationsHeaderEnum::CREATED_AT_HOUR => $carbonInstance->format('H:i')
        ];
    }

    private static function mapSalesman($sale): array
    {
        $firstname = data_get($sale, 'user_firstname');
        $lastname  = data_get($sale, 'user_lastname');

        return [
            MobileApplicationsHeaderEnum::USER_FULL_NAME => $firstname . ' ' . $lastname,
            MobileApplicationsHeaderEnum::USER_CPF       => data_get($sale, 'user_cpf'),
        ];
    }
}

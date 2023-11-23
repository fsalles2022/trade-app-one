<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Exportables;

use Reports\AnalyticalsReports\Input\SalesCollectionMappableInterface;

class AnalyticalReportExport
{
    public static function recordsToArray(SalesCollectionMappableInterface $sales): array
    {
        $toExport = [];
        $rows     = self::collection($sales->toArray());

        $toExport[]  = self::headings();
        $sortedTable = [];

        foreach ($rows as $row) {
            $sortedRow = [];
            foreach (self::headings() as $head) {
                $sortedRow[$head] = $row[$head];
            }
            $sortedTable[] = $sortedRow;
        }

        return array_merge($toExport, $sortedTable);
    }

    public static function collection(array $sales): array
    {
        $rows = [];
        foreach ($sales as $row) {
            $rows[] = AnalyticalReportMapSale::body($row);
        }
        return $rows;
    }

    public static function headings(): array
    {
        return [
            AnalyticalReportHeaderEnum::CHANNEL,
            AnalyticalReportHeaderEnum::SERVICE_SECTOR,
            AnalyticalReportHeaderEnum::SERVICE_OPERATOR,
            AnalyticalReportHeaderEnum::SOURCE,
            AnalyticalReportHeaderEnum::SERVICE_SERVICE_ID,
            AnalyticalReportHeaderEnum::SERVICE_OPERATOR_ID,
            AnalyticalReportHeaderEnum::POINTOFSALE_HIERARCHY_LABEL,
            AnalyticalReportHeaderEnum::CREATED_AT_DATE,
            AnalyticalReportHeaderEnum::CREATED_AT_HOUR,
            AnalyticalReportHeaderEnum::SERVICE_AREACODE,
            AnalyticalReportHeaderEnum::SERVICE_MODE,
            AnalyticalReportHeaderEnum::SERVICE_LABEL,
            AnalyticalReportHeaderEnum::SERVICE_OPERATION,
            AnalyticalReportHeaderEnum::DUE_DAY,
            AnalyticalReportHeaderEnum::SERVICE_INVOICETYPE,
            AnalyticalReportHeaderEnum::SERVICE_STATUS,
            AnalyticalReportHeaderEnum::SERVICE_PRICE,
            AnalyticalReportHeaderEnum::SERVICE_DONATE_CHIP,
            AnalyticalReportHeaderEnum::SERVICE_DISCOUNT_VALUE,
            AnalyticalReportHeaderEnum::SERVICE_RECHARGE,
            AnalyticalReportHeaderEnum::SERVICE_MSISDN,
            AnalyticalReportHeaderEnum::SERVICE_PORTEDNUMBER,
            AnalyticalReportHeaderEnum::SERVICE_ICCID,
            AnalyticalReportHeaderEnum::SERVICE_IMEI,
            AnalyticalReportHeaderEnum::USER_FULL_NAME,
            AnalyticalReportHeaderEnum::USER_CPF,
            AnalyticalReportHeaderEnum::ENROLLMENT,
            AnalyticalReportHeaderEnum::SERVICE_RECURRENCE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_FULL_NAME,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_BIRTH,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_CPF,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_DATE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_LOCAL,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_STATE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_LOCAL,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_LOCAL_NUMBER,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_CITY,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_STATE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_ZIPCODE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_TYPE_OF_ADDRESS,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_COMPLEMENT,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_EMAIL,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_MAIN_PHONE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_SECONDARY_PHONE,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_FILIATION,
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_NAME_1,
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_RG_1,
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_NAME_2,
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_RG_2,
            AnalyticalReportHeaderEnum::POINTOFSALE_LABEL,
            AnalyticalReportHeaderEnum::POINTOFSALE_CNPJ,
            AnalyticalReportHeaderEnum::POINTOFSALE_CITY,
            AnalyticalReportHeaderEnum::POINTOFSALE_STATE,
            AnalyticalReportHeaderEnum::POINTOFSALE_NETWORK_LABEL,
            AnalyticalReportHeaderEnum::POINTOFSALE_NETWORK_SLUG,
            AnalyticalReportHeaderEnum::POINTOFSALE_SLUG,
            AnalyticalReportHeaderEnum::CUSTOMER_TYPE,
            AnalyticalReportHeaderEnum::SERVICE_OPERATOR_STATUS,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_SKU,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_MODEL,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_PRICEWITH,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_PRICEWITHOUT,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_DISCOUNT,
            AnalyticalReportHeaderEnum::UPDATED,
            AnalyticalReportHeaderEnum::SERVICE_ACCEPTANCE,
            AnalyticalReportHeaderEnum::USER_ASSOCIATIVE,
            AnalyticalReportHeaderEnum::HAS_RECOMMENDATION,
            AnalyticalReportHeaderEnum::RECOMMENDATION_REGISTRATION,
            AnalyticalReportHeaderEnum::RECOMMENDATION_NAME,
            AnalyticalReportHeaderEnum::BKO_OBSERVATION
        ];
    }
}

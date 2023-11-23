<?php

namespace Reports\AnalyticalsReports\MobileApplications;

use Carbon\Carbon;
use Generali\Enumerators\GeneraliProductsEnumerators;
use Illuminate\Support\Arr;

class InsuranceEletronicsMapSale
{
    public static function recordsToArray(array $sales): array
    {
        $header = [];
        $rows   = [];

        $header[]    = self::headings();
        $collections = self::collection($sales);

        foreach ($collections as $collection) {
            $rows[] = $collection;
        }

        return array_merge($header, $rows);
    }

    private static function collection(array $sales): array
    {
        $salesToExport = [];

        foreach ($sales as $sale) {
            $salesToExport[] = self::adapterRow($sale);
        }

        return $salesToExport;
    }

    private static function headings(): array
    {
        return [
            InsuranceEletronicsHeaderEnum::REPRESENTATIVE,
            InsuranceEletronicsHeaderEnum::POINT_OF_SALE_CNPJ,
            InsuranceEletronicsHeaderEnum::RAMO,
            InsuranceEletronicsHeaderEnum::COVERAGE,
            InsuranceEletronicsHeaderEnum::TICKET,
            InsuranceEletronicsHeaderEnum::POINT_OF_SALE_SLUG,
            InsuranceEletronicsHeaderEnum::EMISSION_DATE,
            InsuranceEletronicsHeaderEnum::START_VALIDITY_DATE,
            InsuranceEletronicsHeaderEnum::END_VALIDITY_DATE,
            InsuranceEletronicsHeaderEnum::SERVICE_CUSTOMER_CPF,
            InsuranceEletronicsHeaderEnum::BIRTHDAY,
            InsuranceEletronicsHeaderEnum::CUSTOMER_NAME,
            InsuranceEletronicsHeaderEnum::CUSTOMER_CITY,
            InsuranceEletronicsHeaderEnum::CUSTOMER_UF,
            InsuranceEletronicsHeaderEnum::CUSTOMER_ZIPCODE,
            InsuranceEletronicsHeaderEnum::CUSTOMER_NEIGHBORHOOD,
            InsuranceEletronicsHeaderEnum::DEVICE_TYPE,
            InsuranceEletronicsHeaderEnum::DEVICE_BRAND,
            InsuranceEletronicsHeaderEnum::DEVICE_MODEL,
            InsuranceEletronicsHeaderEnum::LMI,
            InsuranceEletronicsHeaderEnum::LIQUID_PREMIUM,
            InsuranceEletronicsHeaderEnum::GROSS_PREMIM,
            InsuranceEletronicsHeaderEnum::HAS_RECOMMENDATION,
            InsuranceEletronicsHeaderEnum::RECOMMENDATION_REGISTRATION
        ];
    }

    private static function adapterRow(array $sale): array
    {
        $source = data_get($sale, '_source');

        return [
            InsuranceEletronicsHeaderEnum::TRADE_UP_SERVICES,
            Arr::get($source, 'pointofsale_cnpj'),
            self::mapRamo($source),
            Arr::get($source, 'service_product_label'),
            Arr::get($source, 'service_policyid'),
            Arr::get($source, 'pointofsale_slug'),
            self::formatDate($source, 'created_at'),
            self::formatDate($source, 'service_premium_validity_start'),
            self::formatDate($source, 'service_premium_validity_end'),
            Arr::get($source, 'service_customer_cpf'),
            Arr::get($source, 'service_customer_birthday', ''),
            Arr::get($source, 'service_customer_firstname') . ' ' . Arr::get($source, 'service_customer_lastname'),
            Arr::get($source, 'service_customer_city'),
            Arr::get($source, 'service_customer_state'),
            Arr::get($source, 'service_customer_zipcode'),
            Arr::get($source, 'service_customer_neighborhood'),
            Arr::get($source, 'service_device_type'),
            Arr::get($source, 'service_device_brand'),
            Arr::get($source, 'service_device_model'),
            GeneraliProductsEnumerators::LMI,
            Arr::get($source, 'service_premium_liquid'),
            Arr::get($source, 'service_premium_total'),
            Arr::get($source, 'service_hasrecommendation', false) ?
                'Sim' : 'Não',
            Arr::get($source, 'service_recommendation_registration')
        ];
    }

    private static function mapRamo(array $sale): string
    {
        $productSlug = Arr::get($sale, 'service_product_slug');

        return array_key_exists($productSlug, GeneraliProductsEnumerators::RAMO)
            ? GeneraliProductsEnumerators::RAMO[$productSlug]
            : '';
    }

    private static function formatDate(array $source, string $key): string
    {
        $date = Arr::get($source, $key);

        return  $date
            ? Carbon::parse($date)->format('d/m/Y')
            : '';
    }
}

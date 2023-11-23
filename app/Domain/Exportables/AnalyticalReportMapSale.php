<?php

namespace TradeAppOne\Domain\Exportables;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class AnalyticalReportMapSale
{
    public static function body(array $line): array
    {
        $toExport = [];
        $toExport = array_merge(
            $toExport,
            self::mapChannel($line),
            self::mapServiceHeader($line),
            self::mapServiceOperatorID($line),
            self::mapServiceDates($line),
            self::mapServiceType($line),
            self::mapSalesman($line),
            self::mapRecommendation($line),
            self::mapPointOfSale($line),
            self::mapRecurrence($line),
            self::mapServicePayment($line),
            self::mapCustomer($line),
            self::mapDevice($line),
            self::mapTelecommunicationInfo($line),
            self::mapCustomerType($line),
            self::mapBkoObservation($line)
        );

        $toExport[AnalyticalReportHeaderEnum::SERVICE_OPERATOR_STATUS] = data_get($line, 'service_statusthirdparty', '');

        return $toExport;
    }

    private static function mapChannel(array $row): array
    {
        $pointOfSaleId = data_get($row, 'pointofsale_id');
        $networkId     = PointOfSale::query()->where('id', '=', $pointOfSaleId)
            ->with('network')
            ->get()
            ->pluck('network')
            ->pluck('id')->first();
        if ($networkId === null) {
            return [AnalyticalReportHeaderEnum::CHANNEL => '-'];
        }
        $channels = Network::query()->where('id', '=', $networkId)
            ->get()->first()->channels()->pluck('name');
        $channels = $channels->when($channels->count() > 1, static function ($collection) {
            return $collection->reject(static function ($value) {
                return $value === Channels::MASTER_DEALER;
            });
        })->when($channels->count() === 0, static function ($collection) {
            return $collection->push('-');
        });
        return [AnalyticalReportHeaderEnum::CHANNEL => $channels->first()];
    }

    private static function mapServiceHeader($row): array
    {
        $serviceSector = data_get($row, 'service_sector');
        return [
            AnalyticalReportHeaderEnum::SERVICE_SECTOR     => trans("status.{$serviceSector}"),
            AnalyticalReportHeaderEnum::SERVICE_OPERATOR   => data_get($row, 'service_operator'),
            AnalyticalReportHeaderEnum::SOURCE             => data_get($row, 'source') ?? trans('status.WEB'),
            AnalyticalReportHeaderEnum::SERVICE_SERVICE_ID => data_get($row, 'service_servicetransaction')
        ];
    }

    private static function mapServiceOperatorID($row): array
    {
        $operatorId = '';
        switch (data_get($row, 'service_operator')) {
            case Operations::CLARO:
                $operatorId = data_get($row, 'service_operatoridentifiers_servico_id');
                break;
            case Operations::VIVO:
                $operatorId = data_get($row, 'service_operatoridentifiers_idvenda');
                break;
            case Operations::NEXTEL:
                $operatorId = data_get($row, 'service_operatoridentifiers_numeropedido');
                break;
            case Operations::TIM:
                $operatorId = data_get($row, 'service_operatoridentifiers_protocol');
                break;
            case Operations::OI:
                if (data_get($row, 'service_operation') === Operations::OI_CONTROLE_CARTAO) {
                    $operatorId = data_get($row, 'service_operatoridentifiers_ref');
                }
                if (data_get($row, 'service_operation') === Operations::OI_CONTROLE_BOLETO) {
                    $operatorId = self::getServiceOperatorIDOiControleBoleto($row);
                }
                break;
        }
        return [
            AnalyticalReportHeaderEnum::SERVICE_OPERATOR_ID => $operatorId
        ];
    }

    private static function getServiceOperatorIDOiControleBoleto(array $row)
    {
        $logs = data_get($row, 'service_log.*.indetificadorAdesao', []);

        return end($logs);
    }

    private static function mapServiceDates($row): array
    {
        $carbonInstance     = Carbon::parse(data_get($row, 'created_at'));
        $carbonInstance->tz = config('app.timezone');

        $carbonUpdated     = Carbon::parse(data_get($row, 'updated_at'));
        $carbonUpdated->tz = config('app.timezone');
        return [
            AnalyticalReportHeaderEnum::CREATED_AT_DATE => $carbonInstance->format('d/m/Y'),
            AnalyticalReportHeaderEnum::CREATED_AT_HOUR => $carbonInstance->format('H:i'),
            AnalyticalReportHeaderEnum::UPDATED         => $carbonUpdated->format('d/m/Y H:i'),
        ];
    }

    private static function mapServiceType($row): array
    {
        $service_mode = data_get($row, 'service_mode');

        return [
            AnalyticalReportHeaderEnum::SERVICE_AREACODE => data_get($row, 'service_areacode')
                ?? substr(data_get($row, 'service_msisdn'), 0, 2)
                ?? substr(data_get($row, 'service_portednumber'), 0, 2),
            AnalyticalReportHeaderEnum::SERVICE_MODE      => trans("status.{$service_mode}"),
            AnalyticalReportHeaderEnum::SERVICE_LABEL     => data_get($row, 'service_label'),
            AnalyticalReportHeaderEnum::SERVICE_OPERATION => data_get($row, 'service_operation')
        ];
    }

    private static function mapSalesman($row): array
    {
        $user_firstName        = data_get($row, 'user_firstname');
        $user_lastMame         = data_get($row, 'user_lastname');
        $associative_firstName = data_get($row, 'user_associative_firstName');
        $associative_lastName  = data_get($row, 'user_associative_lastName');

        return [
            AnalyticalReportHeaderEnum::USER_FULL_NAME   => "$user_firstName  $user_lastMame",
            AnalyticalReportHeaderEnum::USER_CPF         => data_get($row, 'user_cpf'),
            AnalyticalReportHeaderEnum::ENROLLMENT       => data_get($row, 'userAlternate_document'),
            AnalyticalReportHeaderEnum::USER_ASSOCIATIVE => "$associative_firstName $associative_lastName"
        ];
    }

    private static function mapRecommendation(array $row): array
    {

        return Arr::get($row, 'service_operator') === Operations::TIM
            ? self::mapPromoterTim($row)
            : [
                AnalyticalReportHeaderEnum::HAS_RECOMMENDATION => data_get($row, 'service_hasrecommendation', false) ?
                'Sim' : 'Não',
                AnalyticalReportHeaderEnum::RECOMMENDATION_REGISTRATION => data_get($row, 'service_recommendation_registration'),
                AnalyticalReportHeaderEnum::RECOMMENDATION_NAME => ''
            ];
    }

    private static function mapPointOfSale($row): array
    {
        return [
            AnalyticalReportHeaderEnum::POINTOFSALE_HIERARCHY_LABEL => data_get($row, 'pointofsale_hierarchy_label', '-'),
            AnalyticalReportHeaderEnum::POINTOFSALE_LABEL           => data_get($row, 'pointofsale_label'),
            AnalyticalReportHeaderEnum::POINTOFSALE_CNPJ            => data_get($row, 'pointofsale_cnpj'),
            AnalyticalReportHeaderEnum::POINTOFSALE_CITY            => data_get($row, 'pointofsale_city'),
            AnalyticalReportHeaderEnum::POINTOFSALE_STATE           => data_get($row, 'pointofsale_state'),
            AnalyticalReportHeaderEnum::POINTOFSALE_NETWORK_LABEL   => data_get($row, 'pointofsale_network_label'),
            AnalyticalReportHeaderEnum::POINTOFSALE_NETWORK_SLUG    => data_get($row, 'pointofsale_network_slug'),
            AnalyticalReportHeaderEnum::POINTOFSALE_SLUG            => data_get($row, 'pointofsale_slug')
        ];
    }

    private static function mapServicePayment($row): array
    {
        $service_status     = data_get($row, 'service_status');
        $donateChipDiscount = data_get($row, 'service_donate_chip_discount');
        return [
            AnalyticalReportHeaderEnum::SERVICE_INVOICETYPE => data_get($row, 'service_invoicetype'),
            AnalyticalReportHeaderEnum::SERVICE_PRICE       => data_get($row, 'service_price'),
            AnalyticalReportHeaderEnum::SERVICE_DONATE_CHIP => $donateChipDiscount ? 'SIM' : 'NÃO',
            AnalyticalReportHeaderEnum::SERVICE_DISCOUNT_VALUE => $donateChipDiscount ?? '',
            AnalyticalReportHeaderEnum::SERVICE_RECHARGE    => data_get($row, 'services_rechargeValue'),
            AnalyticalReportHeaderEnum::SERVICE_STATUS      => $service_status == '-' ? '-' : trans("status.{$service_status}")
        ];
    }

    private static function mapCustomer($row): array
    {
        $service_customer_firstname = data_get($row, 'service_customer_firstname');
        $service_customer_lastname  = data_get($row, 'service_customer_lastname');

        return [
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_FULL_NAME => $service_customer_firstname . ' ' .
                $service_customer_lastname,
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_CPF              => data_get($row, 'service_customer_cpf'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_LOCAL            => data_get($row, 'service_customer_local'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_LOCAL_NUMBER     => data_get($row, 'service_customer_number'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_CITY             => data_get($row, 'service_customer_city'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_STATE            => data_get($row, 'service_customer_state'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_ZIPCODE          => data_get($row, 'service_customer_zipcode'),
            AnalyticalReportHeaderEnum::DUE_DAY                           => data_get($row, 'service_due_date'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_BIRTH            => data_get($row, 'service_customer_birthday'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG               => data_get($row, 'service_customer_rg'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_DATE          => data_get($row, 'service_customer_rg_date'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_LOCAL         => data_get($row, 'service_customer_rg_local'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_RG_STATE         => data_get($row, 'service_customer_rg_state'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_TYPE_OF_ADDRESS  => data_get($row, 'service_customer_local_id'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_COMPLEMENT       => data_get($row, 'service_customer_complement'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_EMAIL            => data_get($row, 'service_customer_email'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_MAIN_PHONE       => data_get($row, 'service_customer_mainphone'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_SECONDARY_PHONE  => data_get($row, 'service_customer_secondary_phone'),
            AnalyticalReportHeaderEnum::SERVICE_CUSTOMER_FILIATION        => data_get($row, 'service_customer_filiation'),
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_NAME_1            => data_get($row, 'service_customer_witness_name_1'),
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_RG_1              => data_get($row, 'service_customer_witness_rg_1'),
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_NAME_2            => data_get($row, 'service_customer_witness_name_2'),
            AnalyticalReportHeaderEnum::SERVICE_WITNESS_RG_2              => data_get($row, 'service_customer_witness_rg_2'),
        ];
    }

    private static function mapDevice($row): array
    {
        $deviceLabel = data_get($row, 'service_device_label');
        $deviceModel = data_get($row, 'service_device_model');

        return [
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_SKU          => data_get($row, 'service_device_sku'),
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_MODEL        => $deviceLabel ?? $deviceModel,
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_PRICEWITH    => data_get($row, 'service_device_pricewith'),
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_PRICEWITHOUT => data_get($row, 'service_device_pricewithout'),
            AnalyticalReportHeaderEnum::SERVICE_DEVICE_DISCOUNT     => data_get($row, 'service_device_discount')
        ];
    }

    /**
     * @param mixed[] $row
     * @return string[]
     */
    private static function mapBkoObservation(array $row): array
    {
        return [
            AnalyticalReportHeaderEnum::BKO_OBSERVATION => self::concatenateObservations($row)
        ];
    }

    private static function mapTelecommunicationInfo($row): array
    {
        return [
            AnalyticalReportHeaderEnum::SERVICE_MSISDN       => substr(data_get($row, 'service_msisdn'), -11),
            AnalyticalReportHeaderEnum::SERVICE_PORTEDNUMBER => data_get($row, 'service_portednumber'),
            AnalyticalReportHeaderEnum::SERVICE_ICCID        => data_get($row, 'service_iccid'),
            AnalyticalReportHeaderEnum::SERVICE_IMEI         => data_get($row, 'service_imei'),
            AnalyticalReportHeaderEnum::SERVICE_ACCEPTANCE   => data_get($row, 'service_operatoridentifiers_acceptance')
        ];
    }

    private static function mapCustomerType($row): array
    {
        $customerType = data_get($row, 'customertype', 'HOLDER');
        $type         = 'TITULAR';
        switch ($customerType) {
            case 'HOLDER':
                $type = 'TITULAR';
                break;
            case 'DEPENDENT':
                $type = 'DEPENDENTE';
                break;
        }
        return [
            AnalyticalReportHeaderEnum::CUSTOMER_TYPE => $type,
        ];
    }

    /*** @param string[] $row @return string[] */
    private static function mapPromoterTim(array $row): array
    {
        $cpfPromoter = Arr::get($row, 'service_promoter_cpf', '');

        return [
            AnalyticalReportHeaderEnum::HAS_RECOMMENDATION => $cpfPromoter ? 'Sim' : 'Não',
            AnalyticalReportHeaderEnum::RECOMMENDATION_REGISTRATION => $cpfPromoter,
            AnalyticalReportHeaderEnum::RECOMMENDATION_NAME => data_get($row, 'service_promoter_name')
        ];
    }

    /** @param mixed[] $row */
    private static function concatenateObservations(array $row): string
    {
        $observations = '';
        foreach ($row['service_observations'] ?? [] as $data) {
            $userNameComment = data_get($data, 'service_username_comment', '');
            $comment         = data_get($data, 'service_comment', '');
            $observations   .= "[{$userNameComment}]: {$comment}; ";
        }
        return $observations;
    }

    /** @param mixed[] $row */
    private static function mapRecurrence(array $row): array
    {
        $recurrence = data_get($row, 'service_recurrence');
        return [
            AnalyticalReportHeaderEnum::SERVICE_RECURRENCE => $recurrence === true ? 'Sim'
                : ($recurrence === null ? '-' : 'Não')
        ];
    }
}

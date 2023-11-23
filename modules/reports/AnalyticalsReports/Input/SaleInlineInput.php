<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\Input;

use Carbon\Carbon;
use Gateway\Enumerators\StatusPaymentTransaction;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class SaleInlineInput implements SaleMappableInterface
{
    private const CUSTOMER_TYPE_HOLDER    = 'HOLDER';
    private const CUSTOMER_TYPE_DEPENDENT = 'DEPENDENT';

    /** @var Sale */
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /** @return mixed[] */
    protected function mapUser(): array
    {
        return [
            'user_id'                    => data_get($this->sale, 'user.id'),
            'user_cpf'                   => data_get($this->sale, 'user.cpf'),
            'user_firstname'             => data_get($this->sale, 'user.firstName'),
            'user_lastname'              => data_get($this->sale, 'user.lastName'),
            'user_role'                  => data_get($this->sale, 'user.role'),
            'user_email'                 => data_get($this->sale, 'user.email'),
            'user_associative_firstName' => data_get($this->sale, 'user.associative.firstName'),
            'user_associative_lastName'  => data_get($this->sale, 'user.associative.lastName'),
        ];
    }

    /** @return mixed[] */
    protected function useAlternate(): array
    {
        return [
            'userAlternate_document' => data_get($this->sale, 'userAlternate.document')
        ];
    }

    /** @return mixed[] */
    protected function mapPointOfSale(): array
    {
        return [
            'pointofsale_id'                             => data_get($this->sale, 'pointOfSale.id'),
            'pointofsale_city'                           => data_get($this->sale, 'pointOfSale.city'),
            'pointofsale_tradingname'                    => data_get($this->sale, 'pointOfSale.tradingName'),
            'pointofsale_areacode'                       => data_get($this->sale, 'pointOfSale.areaCode'),
            'pointofsale_zipcode'                        => data_get($this->sale, 'pointOfSale.zipCode'),
            'pointofsale_cnpj'                           => data_get($this->sale, 'pointOfSale.cnpj'),
            'pointofsale_label'                          => data_get($this->sale, 'pointOfSale.label'),
            'pointofsale_companyname'                    => data_get($this->sale, 'pointOfSale.companyName'),
            'pointofsale_state'                          => data_get($this->sale, 'pointOfSale.state'),
            'pointofsale_slug'                           => data_get($this->sale, 'pointOfSale.slug'),
            'pointofsale_provideridentifiers_nextel_ref' => data_get($this->sale, 'pointOfSale.providerIdentifiers.NEXTEL.ref'),
            'pointofsale_provideridentifiers_nextel_cod' => data_get($this->sale, 'pointOfSale.providerIdentifiers.NEXTEL.cod'),
            'pointofsale_network_id'                     => data_get($this->sale, 'pointOfSale.network.id'),
            'pointofsale_network_cnpj'                   => data_get($this->sale, 'pointOfSale.network.cnpj'),
            'pointofsale_network_slug'                   => data_get($this->sale, 'pointOfSale.network.slug'),
            'pointofsale_network_label'                  => data_get($this->sale, 'pointOfSale.network.label'),
            'pointofsale_network_companyname'            => data_get($this->sale, 'pointOfSale.network.companyName'),
            'pointofsale_hierarchy_id'                   => data_get($this->sale, 'pointOfSale.hierarchy.id'),
            'pointofsale_hierarchy_slug'                 => data_get($this->sale, 'pointOfSale.hierarchy.slug'),
            'pointofsale_hierarchy_label'                => data_get($this->sale, 'pointOfSale.hierarchy.label'),
            'pointofsale_hierarchy_sequence'             => data_get($this->sale, 'pointOfSale.hierarchy.sequence'),
        ];
    }

    /** @return mixed[] */
    protected function mapSale(): array
    {
        return [
            'channel'    => data_get($this->sale, 'channel'),
            'source'     => data_get($this->sale, 'source'),
            'created_at' => data_get($this->sale, 'createdAt'),
            'updated_at' => data_get($this->sale, 'updatedAt'),
        ];
    }

    /** @return mixed[] */
    protected function mapService(Service $service): array
    {
        return [
            'service_label'                             => data_get($service, 'label'),
            'service_status'                            => data_get($service, 'status'),
            'service_sector'                            => data_get($service, 'sector'),
            'service_product'                           => data_get($service, 'product'),
            'service_due_date'                          => data_get($service, 'dueDate'),
            'service_quantity'                          => data_get($service, 'quantity'),
            'service_operator'                          => data_get($service, 'operator'),
            'service_price'                             => data_get($service, 'price'),
            'services_rechargeValue'                    => data_get($service, 'rechargeValue'),
            'service_recurrence'                       => data_get($service, 'recurrence'),
            'service_mode'                              => data_get($service, 'mode'),
            'service_operation'                         => data_get($service, 'operation'),
            'service_areacode'                          => data_get($service, 'areaCode'),
            'service_invoicetype'                       => data_get($service, 'invoiceType'),
            'service_imei'                              => data_get($service, 'imei'),
            'service_msisdn'                            => data_get($service, 'msisdn'),
            'service_portednumber'                      => data_get($service, 'portedNumber'),
            'service_iccid'                             => data_get($service, 'iccid'),
            'service_servicetransaction'                => data_get($service, 'serviceTransaction'),
            'service_statusthirdparty'                  => data_get($service, 'statusThirdParty'),
            'service_hasrecommendation'                 => data_get($service, 'hasRecommendation'),
            'service_promoter_name'                     => data_get($service, 'promoter.name'),
            'service_promoter_cpf'                      => data_get($service, 'promoter.cpf'),
            'service_log'                               => data_get($service, 'log'),
            'service_recommendation_registration'       => data_get($service, 'recommendation.registration'),
            'service_device_label'                      => data_get($service, 'device.label'),
            'service_device_model'                      => data_get($service, 'device.model'),
            'service_device_sku'                        => data_get($service, 'device.sku'),
            'service_device_pricewith'                  => data_get($service, 'device.priceWith'),
            'service_device_pricewithout'               => data_get($service, 'device.priceWithout'),
            'service_device_discount'                   => data_get($service, 'device.discount'),
            'service_donate_chip_discount'              => data_get($service, 'donate_chip.discount'),
            'service_customer_firstname'                => data_get($service, 'customer.firstName'),
            'service_customer_lastname'                 => data_get($service, 'customer.lastName'),
            'service_customer_password'                 => data_get($service, 'customer.password'),
            'service_customer_mainphone'                => data_get($service, 'customer.mainPhone'),
            'service_customer_secondary_phone'          => data_get($service, 'customer.secondaryPhone'),
            'service_customer_cpf'                      => data_get($service, 'customer.cpf'),
            'service_customer_local'                    => data_get($service, 'customer.local'),
            'service_customer_zipcode'                  => data_get($service, 'customer.zipCode'),
            'service_customer_number'                   => data_get($service, 'customer.number'),
            'service_customer_local_id'                 => $this->getLocalIdByService($service),
            'service_customer_city'                     => data_get($service, 'customer.city'),
            'service_customer_state'                    => data_get($service, 'customer.state'),
            'service_customer_email'                    => data_get($service, 'customer.email'),
            'service_customer_birthday'                 => data_get($service, 'customer.birthday'),
            'service_customer_filiation'                => data_get($service, 'customer.filiation'),
            'service_customer_rg'                       => data_get($service, 'customer.rg'),
            'service_customer_rg_date'                  => data_get($service, 'customer.rgDate'),
            'service_customer_rg_local'                 => data_get($service, 'customer.rgLocal'),
            'service_customer_rg_state'                 => data_get($service, 'customer.rgState'),
            'service_customer_witness_rg_1'             => data_get($service, 'customer.witnessRg1'),
            'service_customer_witness_name_1'           => data_get($service, 'customer.witnessName1'),
            'service_customer_witness_rg_2'             => data_get($service, 'customer.witnessRg2'),
            'service_customer_witness_name_2'           => data_get($service, 'customer.witnessName2'),
            'service_customer_complement'               => data_get($service, 'customer.complement'),
            'service_payment_times'                     => data_get($service, 'payment.times'),
            'service_payment_gatewaytransactionid'      => data_get($service, 'payment.gatewayTransactionId'),
            'service_payment_log'                       => $this->logInlinePaymentTransaction($service),
            'service_operatoridentifiers_servico_id'     => data_get($service, 'operatorIdentifiers.servico_id'),
            'service_operatoridentifiers_idvenda'        => data_get($service, 'operatorIdentifiers.venda_id'),
            'service_operatoridentifiers_numeropedido'   => data_get($service, 'operatorIdentifiers.numeroPedido'),
            'service_operatoridentifiers_protocol'       => data_get($service, 'operatorIdentifiers.protocol'),
            'service_operatoridentifiers_ref'            => data_get($service, 'operatorIdentifiers.ref'),
            'service_operatoridentifiers_acceptance'     => data_get($service, 'operatorIdentifiers.acceptance'),
            'customertype'                              => self::CUSTOMER_TYPE_HOLDER,
        ];
    }

    protected function getLocalIdByService(Service $service): string
    {
        $localId = data_get($service, 'customer.localId');

        if (is_array($localId)) {
            $localId = data_get($localId, 'label');
        }

        return (string) $localId;
    }

    /**
     * @param mixed[] $dependent
     * @return mixed[]
     */
    protected function mapServiceDependent(Service $service, array $dependent, int $position = 0): array
    {
        return [
            'service_servicetransaction' => data_get($service, 'serviceTransaction') . "_DEP-{$position}",
            'customertype'               => self::CUSTOMER_TYPE_DEPENDENT,
            'service_mode'               => data_get($dependent, 'mode'),
            'service_product'            => data_get($dependent, 'product'),
            'service_iccid'              => data_get($dependent, 'iccid'),
            'service_label'              => data_get($dependent, 'label'),
            'service_price'              => data_get($dependent, 'price'),
        ];
    }

    protected function serviceHasDependents(Service $service): bool
    {
        $dependents = data_get($service, 'dependents', []);

        return count($dependents) > 0;
    }

    protected function logInlinePaymentTransaction(Service $service): ?string
    {
        $paymentTransactionLog = data_get($service, 'payment.log', false);

        if (is_array($paymentTransactionLog) === false) {
            return null;
        }

        $logInline = '';
        foreach ($paymentTransactionLog as $payment) {
            $status     = StatusPaymentTransaction::translate(data_get($payment, 'status', ''));
            $modifyDate = $this->formatDatePaymentTransaction(data_get($payment, 'createdAt', ''));
            $logInline .= "[STATUS]: {$status}, [CRIADO EM]: {$modifyDate} ;";
        }
        return $logInline;
    }

    protected function formatDatePaymentTransaction(string $date): string
    {
        $carbonInstance     = Carbon::parse($date);
        $carbonInstance->tz = config('app.timezone');
        return $carbonInstance->format('d/m/y H:i');
    }

    /** @return string[] */
    protected function mapBkoObservation(Service $service): array
    {
        $observations = [];
        foreach ($service['backoffice'] ?? [] as $observation) {
            $observations['service_observations'][] = [
                'service_comment' => data_get($observation, 'comment', ''),
                'service_username_comment' => data_get($observation, 'user.firstName', '')
            ];
        }

        return $observations;
    }

    /** @return array[] */
    public function toArray(): array
    {
        $salesInline = [];

        $sale          = $this->mapSale();
        $user          = $this->mapUser();
        $userAlternate = $this->useAlternate();
        $pointOfSale   = $this->mapPointOfSale();

        $services = $this->sale->services ?? [];

        foreach ($services as $service) {
            $serviceMapped = $this->mapService($service);
            $observations  = $this->mapBkoObservation($service);

            $saleMapped = array_merge(
                $sale,
                $user,
                $userAlternate,
                $pointOfSale,
                $serviceMapped,
                $observations
            );

            $salesInline[] = $saleMapped;

            if ($this->serviceHasDependents($service) === false) {
                continue;
            }

            $dependents = $service->dependents ?? [];

            foreach ($dependents as $key => $dependent) {
                $dependentMapped = $this->mapServiceDependent($service, $dependent, $key);

                $salesInline[] = array_merge(
                    $saleMapped,
                    $dependentMapped
                );
            }
        }

        return $salesInline;
    }
}

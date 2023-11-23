<?php

declare(strict_types=1);

namespace Buyback\Exportables\Input;

use Reports\AnalyticalsReports\Input\SaleMappableInterface;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class TradeInSaleInlineInput implements SaleMappableInterface
{
    /** @var Sale */
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /** @return mixed[] */
    protected function mapPointOfSale(): array
    {
        return [
            'pointofsale_city'           => data_get($this->sale, 'pointOfSale.city'),
            'pointofsale_zipcode'        => data_get($this->sale, 'pointOfSale.zipCode'),
            'pointofsale_cnpj'           => data_get($this->sale, 'pointOfSale.cnpj'),
            'pointofsale_local'          => data_get($this->sale, 'pointOfSale.local'),
            'pointofsale_slug'           => data_get($this->sale, 'pointOfSale.slug'),
            'pointofsale_number'         => data_get($this->sale, 'pointOfSale.number'),
            'pointofsale_network_label'  => data_get($this->sale, 'pointOfSale.network.label'),
        ];
    }

    /** @return string[] */
    public function toArray(): array
    {
        $salesInline = [];

        $sale        = $this->mapSale();
        $user        = $this->mapUser();
        $pointOfSale = $this->mapPointOfSale();

        $services = $this->sale->services ?? [];

        foreach ($services as $service) {
            $serviceMapped = $this->mapService($service);

            $saleMapped = array_merge(
                $sale,
                $user,
                $pointOfSale,
                $serviceMapped
            );

            $salesInline[] = $saleMapped;
        }

        return $salesInline;
    }

    /** @return mixed[] */
    protected function mapService(Service $service): array
    {
        $priceSalesman  = data_get($service, 'evaluations.salesman.price', 0) ?? 0;
        $priceAppraiser = data_get($service, 'evaluations.appraiser.price', 0) ?? 0;

        return [
            'service_imei'                                  => data_get($service, 'imei'),
            'service_servicetransaction'                    => data_get($service, 'serviceTransaction'),
            'service_hasrecommendation'                     => data_get($service, 'hasRecommendation'),
            'service_recommendation_registration'           => data_get($service, 'recommendation.registration'),
            'service_operation'                             => data_get($service, 'operation'),
            'service_status'                                => data_get($service, 'status'),
            'service_device_id'                             => data_get($service, 'device.id'),
            'service_device_storage'                        => data_get($service, 'device.storage'),
            'service_device_model'                          => data_get($service, 'device.model'),
            'service_device_color'                          => data_get($service, 'device.color'),
            'service_price'                                 => data_get($service, 'price'),
            'service_evaluations_salesman_price'            => $priceSalesman,
            'service_evaluations_appraiser_price'           => $priceAppraiser,
            'service_evaluations_appraiser_created_at'      => data_get($service, 'evaluations.appraiser.createdAt'),
            'service_evaluations_appraiser_user_first_name' => data_get($service, 'evaluations.appraiser.user.firstName'),
            'service_evaluations_appraiser_user_last_name'  => data_get($service, 'evaluations.appraiser.user.lastName'),
            'service_evaluations_carrier_price'             => data_get($service, 'evaluations.carrier.price'),
            'service_evaluations_salesman_appraiser_diff'   => ($priceSalesman - $priceAppraiser),
            'service_customer_firstname'                    => data_get($service, 'customer.firstName'),
            'service_customer_lastname'                     => data_get($service, 'customer.lastName'),
            'service_customer_cpf'                          => data_get($service, 'customer.cpf'),
            'service_customer_local'                        => data_get($service, 'customer.local'),
            'service_customer_zipcode'                      => data_get($service, 'customer.number'),
            'service_customer_number'                       => data_get($service, 'customer.zipCode'),
            'service_customer_city'                         => data_get($service, 'customer.city'),
            'service_customer_state'                        => data_get($service, 'customer.state'),
            'service_customer_email'                        => data_get($service, 'customer.email'),
            'service_customer_complement'                   => data_get($service, 'customer.complement'),
            'service_waybill_id'                            => data_get($service, 'waybill.id'),
            'service_waybill_withdrawn'                     => data_get($service, 'waybill.withdrawn'),
            'service_waybill_printed_at'                    => data_get($service, 'waybill.printedAt'),
            'service_evaluations_salesman_questions'        => data_get($service, 'evaluations.salesman.questions', []),
            'service_evaluations_appraiser_questions'       => data_get($service, 'evaluations.appraiser.questions', []),
            'service_evaluations_bonus'                     => data_get($service, 'evaluationsBonus', [])
        ];
    }

    /** @return string[] */
    protected function mapSale(): array
    {
        return [
            'created_at' => data_get($this->sale, 'createdAt'),
        ];
    }

    /** @return string[] */
    protected function mapUser(): array
    {
        return [
            'user_firstname' => data_get($this->sale, 'user.firstName', ''),
            'user_lastname'  => data_get($this->sale, 'user.lastName', '')
        ];
    }
}

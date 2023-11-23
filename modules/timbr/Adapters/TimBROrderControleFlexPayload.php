<?php

declare(strict_types=1);

namespace TimBR\Adapters;

use TimBR\Enumerators\TimBRFormats;
use TimBR\Enumerators\TimBRSegments;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBROrderControleFlexPayload
{
    /** @return array[] */
    public static function adapt(Service $service): array
    {
        $service = self::adaptService($service);

        $packages = [];

        if (! empty(data_get($service, 'package.id'))) {
            $packages[] = [
                "id" => data_get($service, 'package.id'),
            ];
        }

        if (! empty(data_get($service, 'automaticPackages'))) {
            $packages = array_merge(
                $packages,
                data_get($service, 'automaticPackages')
            );
        }

        $services = [];

        // Selected Services by customer
        if (! empty(data_get($service, 'selectedServices'))) {
            $services = array_merge(
                $services,
                array_map(
                    function (array $selectedService) {
                        return [
                            'id' => $selectedService['id'] ?? null
                        ];
                    },
                    data_get($service, 'selectedServices')
                )
            );
        }

        return [
            'order' => [
                'isSimulation' => false,
                'eligibilityToken' => data_get($service, 'eligibilityToken', ''),
                'pdv' => self::getPdv($service->sale),
                'customer' => self::getCustomer($service->customer),
                'contract' => array_filter(['msisdn' => $service->msisdn ?? '']),
                'plan' => ['segment' => TimBRSegments::CONTROLE_FLEX, 'id' => $service->product ?? null],
                'newContract' => self::getNewContract($service),
                'portability' => array_filter(['msisdn' => $service['portedNumber'] ?? null]),
                'loyalties' => array_filter([array_filter(['id' => $service['loyalty']['id'] ?? null])]),
                'offers' => [],
                'optin' => [['blockMessage' => 'Mensagem MKT', 'option' => false]],
                'witness' => [],
                'contractDocumentation' => ['shippingType' => 'mail', 'printType' => 'normal'],
                "packages" => array_filter(collect($packages)->unique(['id'])->values()->all()),
                "services" => array_filter(collect($services)->unique(['id'])->values()->all()),
                'vendor' => array_filter(['nominativeVendor' => $service['promoter']['cpf'] ?? null])
            ],

        ];
    }

    private static function adaptService(Service $service): Service
    {
        $mode = $service['mode'] ?? null;

        if ($mode === Modes::PORTABILITY || $mode === Modes::ACTIVATION) {
            $service['msisdn'] = '';
        }

        if ($mode === Modes::MIGRATION) {
            $service['iccid'] = '';
        }

        return $service;
    }

    /** @return mixed[] */
    private static function getNewContract(Service $service): array
    {
        return array_filter([
            'ddd'     => $service['areaCode'] ?? null,
            'simCard' => ['id' => $service['iccid'] ?? null]
        ]);
    }

    /** @return mixed[] */
    private static function getPdv(Sale $sale): array
    {
        return [
            'custCode'  => $sale->pointOfSale['providerIdentifiers'][Operations::TIM],
            'stateCode' => $sale->pointOfSale['state'],
        ];
    }

    /**
     * @param mixed[] $customer
     * @return mixed[]
     */
    private static function getCustomer(array $customer): array
    {
        return [
            'address' => self::mountCustomerAddress($customer),
            'socialSecNo' => $customer['cpf'] ?? null,
            'name' => $customer['firstName'] ?? '' . $customer['lastName'] ?? '',
            'customerType'     => 'PF',
            'motherName'       => $customer['filiation'] ?? null,
            'gender'           => $customer['gender'] ?? null,
            'isIlliterate'     => false,
            'birthDate'        => date(TimBRFormats::DATES, strtotime($customer['birthday'] ?? '')),
            'country' => 'Brasil',
            'contactNumber'    => substr($customer['mainPhone'], 3) ?? '',
            'email'            => $customer['email'] ?? null,
            'disabilities'     => [],
            'identityDocument' => array_filter([
                'type'            => 'Carteira de Identidade',
                'number'          => (string) $customer['rg'],
                'issueDate'       => date(TimBRFormats::DATES, strtotime($customer['rgDate'] ?? '')),
                'issuerAgency'    => $customer['rgLocal'],
                'issuerStateCode' => $customer['rgState'],
            ])
        ];
    }

    /**
     * @param mixed[] $customer
     * @return mixed[]
     */
    private static function mountCustomerAddress(array $customer): array
    {
        return [
            'postalCode'   => $customer['zipCode'] ?? null,
            'streetType'   => $customer['localId'] ?? null,
            'streetName'   => $customer['local'] ?? null,
            'number'       => $customer['number'] ?? null,
            'neighborhood' => $customer['neighborhood'] ?? null,
            'cityName'     => $customer['city'] ?? null,
            'stateCode'    => $customer['state'] ?? null,
            'country'      => 'Brasil',
            'complement'   => $customer['complement'] ?? null,
        ];
    }
}

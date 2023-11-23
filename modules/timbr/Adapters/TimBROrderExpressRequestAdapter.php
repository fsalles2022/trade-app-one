<?php

namespace TimBR\Adapters;

use TimBR\Enumerators\TimBRFormats;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBROrderExpressRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $sale     = $service->sale;
        $customer = $service->customer;
        $date     = date(TimBRFormats::DATES, strtotime($customer['birthday']));
        $rgDate   = date(TimBRFormats::DATES, strtotime($customer['rgDate']));

        $address = self::mountCustomerAddress($customer);

        switch ($service['mode']) {
            case Modes::PORTABILITY:
            case Modes::ACTIVATION:
                $areaCode          = $service['areaCode'];
                $service['msisdn'] = '';
                break;
            case Modes::MIGRATION:
                $service['iccid'] = '';
                break;
        }

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

        return array_filter([
            'order' => array_filter([
                'isSimulation'     => false,
                'eligibilityToken' => data_get($service, 'eligibilityToken', ''),
                'protocol'         => data_get($service, 'operatorIdentifiers.protocol', ''),
                'pdv'              => [
                    'custCode'  => $sale->pointOfSale['providerIdentifiers'][Operations::TIM],
                    'stateCode' => $sale->pointOfSale['state'],
                ],
                'customer'              => array_filter([
                    'address'          => $address,
                    'socialSecNo'      => $customer['cpf'],
                    'name'             => "{$customer['firstName']} {$customer['lastName']}",
                    'customerType'     => 'PF',
                    'motherName'       => $customer['filiation'],
                    'gender'           => $customer['gender'],
                    'isIlliterate'     => false,
                    'birthDate'        => (string) $date,
                    'country'          => 'Brasil',
                    'contactNumber'    => substr($customer['mainPhone'], 3) ?? '',
                    'email'            => $customer['email'],
                    'disabilities'     => [],
                    'identityDocument' => array_filter([
                        'type'            => 'Carteira de Identidade',
                        'number'          => (string) $customer['rg'],
                        'issueDate'       => $rgDate,
                        'issuerAgency'    => $customer['rgLocal'],
                        'issuerStateCode' => $customer['rgState'],
                    ])
                ]),
                'contract'              => array_filter([
                    'msisdn' => $service->msisdn ?? '',
                ]),
                'plan'                  => [
                    'segment' => 'EXPRESS',
                    'id'      => $service->product
                ],
                'newContract'           => array_filter([
                    'ddd'     => $areaCode ?? '',
                    'simCard' => ['id' => $service['iccid'] ?? '']
                ]),
                'portability'           => array_filter([
                    'msisdn' => $service->portedNumber ?? '',
                ]),
                'loyalties'             => array_filter([
                    array_filter([
                        'id' => $service['loyalty']['id'] ?? ''
                    ])
                ]),
                'offers'                => array_filter([
                    array_filter([
                        'id' => ''
                    ])
                ]),
                'optin'                 => [
                    [
                        'blockMessage' => 'Mensagem MKT',
                        'option'       => false
                    ]
                ],
                'witness'               => array_filter([
                    array_filter([
                        'identityDocument' => array_filter([
                            'number' => ''
                        ])
                    ])
                ]),
                'contractDocumentation' => [
                    'shippingType' => 'mail',
                    'printType'    => 'normal'
                ],
                "packages" => array_filter(collect($packages)->unique(['id'])->values()->all()),
                "services" => array_filter(collect($services)->unique(['id'])->values()->all()),
                'vendor' => array_filter([
                    'nominativeVendor' => data_get($service, 'promoter.cpf')
                ])
            ])
        ]);
    }

    private static function mountCustomerAddress(array $customer): array
    {
        return [
            'postalCode'   => data_get($customer, 'zipCode'),
            'streetType'   => data_get($customer, 'localId'),
            'streetName'   => data_get($customer, 'local'),
            'number'       => data_get($customer, 'number'),
            'neighborhood' => data_get($customer, 'neighborhood'),
            'cityName'     => data_get($customer, 'city'),
            'stateCode'    => data_get($customer, 'state'),
            'country'      => 'Brasil',
            'complement'   => data_get($customer, 'complement', ''),
        ];
    }
}

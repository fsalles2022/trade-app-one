<?php

namespace TimBR\Adapters;

use TimBR\Enumerators\TimBRFormats;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;

class TimBROrderRequestAdapterV1 implements RequestAdapterBehavior
{
    protected const SEGMENT = [
        Operations::TIM_CONTROLE_FATURA => 'CONTROLE',
        Operations::TIM_EXPRESS => 'EXPRESS',
    ];

    public static function adapt(Service $service, $extra = null): array
    {
        $sale     = $service->sale;
        $customer = $service->customer;
        $date     = date(TimBRFormats::DATES, strtotime($customer['birthday']));
        $rgDate   = date(TimBRFormats::DATES, strtotime($customer['rgDate']));
        $address  = [
            'postalCode'   => $customer['zipCode'],
            'streetType'   => $customer['localId'],
            'streetName'   => $customer['local'],
            'number'       => $customer['number'],
            'neighborhood' => $customer['neighborhood'],
            'cityName'     => $customer['city'],
            'stateCode'    => $customer['state'],
            'country'      => 'Brasil',
            'complement'   => $customer['complement'] ?? '',
        ];

        if ($service['operation'] !== null && array_key_exists($service['operation'], self::SEGMENT)) {
            $segment = self::SEGMENT[$service['operation']];
        }

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

        return array_filter([
            'order' => array_filter([
                'isSimulation'     => false,
                'eligibilityToken' => $service->eligibilityToken,
                'pdv'              => [
                    'custCode'  => $sale->pointOfSale['providerIdentifiers'][Operations::TIM],
                    'stateCode' => $sale->pointOfSale['state'],
                ],

                'customer'              => [
                    'address'          => $address,
                    'socialSecNo'      => $customer['cpf'],
                    'name'             => "{$customer['firstName']} {$customer['lastName']}",
                    "customerType"     => "PF",
                    'motherName'       => $customer['filiation'],
                    'gender'           => $customer['gender'],
                    "isIlliterate"     => false,
                    'birthDate'        => (string) $date,
                    "country"          => "Brasil",
                    "contactNumber"    => substr($customer['mainPhone'], 3) ?? '',
                    'email'            => $customer['email'],
                    "disabilities"     => [],
                    'identityDocument' => array_filter([
                        'type'            => 'Carteira de Identidade',
                        'number'          => (string) $customer['rg'],
                        "issueDate"       => $rgDate,
                        "issuerAgency"    => $customer['rgLocal'],
                        "issuerStateCode" => $customer['rgState'],
                    ]),
                ],
                'contract'              => array_filter([
                    'msisdn' => $service->msisdn ?? '',
                ]),
                "plan"                  => [
                    "segment" => $segment,
                    "id"      => $service->product
                ],
                "newContract"           => array_filter([
                    "ddd"     => $areaCode ?? '',
                    "simCard" => $service['iccid'] ?? ''
                ]),
                "portability"           => array_filter([
                    'msisdn' => $service->portedNumber ?? '',
                ]),
                "loyalties"             => array_filter([
                    array_filter([
                        "id" => $service['loyalty']['id'] ?? ''
                    ])
                ]),
                "offers"                => array_filter([
                    array_filter([
                        "id" => ""
                    ])
                ]),
                "optin"                 => [
                    [
                        "blockMessage" => "Mensagem MKT",
                        "option"       => false
                    ]
                ],
                "witness"               => array_filter([
                    array_filter([
                        "identityDocument" => array_filter([
                            "number" => ""
                        ])
                    ])
                ]),
                "contractDocumentation" => [
                    "shippingType" => "mail",
                    "printType"    => "normal"
                ],
                "billingProfile"        => [
                    "address"       => $address,
                    "billType"      => $service['billType'],
                    "paymentMethod" => $service['invoiceType'],
                    "dueDate"       => $service['dueDate'],
                    "email"         => $customer['email'],
                    "directDebit"   => [
                        "bankCode" => is_null(data_get($service, 'directDebit.bankId'))
                            ? ''
                            : data_get($service, 'directDebit.bankId', ''),

                        "accountNumber" => is_null(data_get($service, 'directDebit.checkingAccount'))
                            ? ''
                            : data_get($service, 'directDebit.checkingAccount', ''),

                        "agencyCode" => is_null(data_get($service, 'directDebit.agency'))
                            ? ''
                            : data_get($service, 'directDebit.agency', ''),

                        "operationCode" => '',
                    ],
                ],
                "acceptanceType"        => "VOZ",
            ])
        ]);
    }
}

<?php

namespace TimBR\Adapters;

use stdClass;
use TimBR\Enumerators\ResolvingTimOfferMistake;
use TimBR\Enumerators\TimBRFormats;
use TimBR\Enumerators\TimBRSegments;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Exceptions\BuildExceptions;

class TimBROrderRequestAdapter implements RequestAdapterBehavior
{
    protected const SEGMENT = [
        Operations::TIM_CONTROLE_FATURA         => 'CONTROLE',
        Operations::TIM_EXPRESS                 => 'EXPRESS',
        Operations::TIM_PRE_PAGO                => 'PRE_PAGO',
        Operations::TIM_CONTROLE_FLEX           => 'CONTROLE_FLEX',
        Operations::TIM_BLACK                   => TimBRSegments::TRANSLATE[Operations::TIM_BLACK],
        Operations::TIM_BLACK_EXPRESS           => TimBRSegments::TRANSLATE[Operations::TIM_BLACK_EXPRESS],
        Operations::TIM_BLACK_MULTI             => TimBRSegments::TRANSLATE[Operations::TIM_BLACK_MULTI],
        Operations::TIM_BLACK_MULTI_DEPENDENT   => TimBRSegments::TRANSLATE[Operations::TIM_BLACK_MULTI_DEPENDENT],
    ];

    /** @return mixed[] */
    public static function adapt(Service $service, $extra = null): array
    {
        $sale     = $service->sale;
        $customer = $service->customer;
        $date     = date(TimBRFormats::DATES, strtotime($customer['birthday']));

        $rgDate = data_get($customer, 'rgDate');

        if ($rgDate !== null) {
            $rgDate = date(TimBRFormats::DATES, strtotime($rgDate));
        }

        $address = array_filter([
            'postalCode'   => data_get($customer, 'zipCode'),
            'streetType'   => data_get($customer, 'localId'),
            'streetName'   => data_get($customer, 'local'),
            'number'       => data_get($customer, 'number'),
            'neighborhood' => data_get($customer, 'neighborhood'),
            'cityName'     => data_get($customer, 'city'),
            'stateCode'    => data_get($customer, 'state'),
            'country'      => 'Brasil',
            'complement'   => data_get($customer, 'complement', ''),
        ]);

        if ($service['operation'] !== null && array_key_exists($service['operation'], self::SEGMENT)) {
            $segment = self::SEGMENT[$service['operation']];
        }

        $msisdnForMigrate = '';

        switch ($service['mode']) {
            case Modes::PORTABILITY:
            case Modes::ACTIVATION:
                $areaCode = $service['areaCode'];

                break;
            case Modes::MIGRATION:
                $msisdnForMigrate = $service['msisdn'];
                $service['iccid'] = '';
                break;
        }

        $packages = [];

        // Default Package
        if (! empty(data_get($service, 'package.id'))) {
            $packages[] = [
                "id" => data_get($service, 'package.id'),
            ];
        }

        // Automatic Packages
        if (! empty(data_get($service, 'automaticPackages'))) {
            $packages = array_merge(
                $packages,
                data_get($service, 'automaticPackages')
            );
        }

        // Selected Packages by customer
        if (! empty(data_get($service, 'selectedPackages'))) {
            $packages = array_merge(
                $packages,
                array_map(
                    function (array $selectedPackage) {
                        return [
                            'id' => $selectedPackage['id'] ?? null
                        ];
                    },
                    data_get($service, 'selectedPackages')
                )
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

        $groupMemberType = '';

        if ($service->operation === Operations::TIM_BLACK_MULTI) {
            $groupMemberType = 'FAMILIA MASTER';
        }

        if ($service->operation === Operations::TIM_BLACK_MULTI_DEPENDENT) {
            $groupMemberType = 'FAMILIA DEPENDENTE';
        }

        $order = array_filter([
            'order' => array_filter([
                'isSimulation'     => false,
                'eligibilityToken' => $service->eligibilityToken,
                'protocol'         => data_get($service, 'operatorIdentifiers.protocol', ''),
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
                        'number'          => (string) data_get($customer, 'rg'),
                        "issueDate"       => $rgDate,
                        "issuerAgency"    => data_get($customer, 'rgLocal'),
                        "issuerStateCode" => data_get($customer, 'rgState'),
                    ]),
                ],
                'contract'              => array_filter([
                    'msisdn' => $msisdnForMigrate,
                ]),
                "plan"                  => [
                    "segment" => $segment,
                    "id"      => $service->product
                ],
                "newContract"           => array_filter([
                    "ddd"     => $areaCode ?? '',
                    "simCard" => array_filter([
                        'id' => $service['iccid'] ?? ''
                    ]),
                    'groupMember' => array_filter([
                        'type' => $groupMemberType,
                        'master' => array_filter([
                            'msisdn' => $service->masterNumber ?? ''
                        ])
                    ]),
                ]),
                "portability"           => array_filter([
                    'msisdn' => $service->portedNumber ?? '',
                ]),
                "loyalties"             => array_filter(self::getLoyalties($service)),
                "offers"                => array_filter([
                    array_filter(self::choiceOffer($service))
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
                "billingProfile"        => array_filter(self::hasBilling($service, $customer, $address)),
                "acceptanceType"        => self::choiceAcceptance($service),
                "packages"              => array_filter(collect($packages)->unique(['id'])->values()->all()),
                "services"              => array_filter(collect($services)->unique(['id'])->values()->all()),
                'vendor' => array_filter([
                    'nominativeVendor' => data_get($service, 'promoter.cpf')
                ])
            ])
        ]);

        $order['order']['isSimulation'] = false;

        if (! isset($order['order']['contract'])) {
            $order['order']['contract'] = new stdClass();
        }

        return $order;
    }

    public static function choiceOffer($service): array
    {
        $areaCode  = data_get($service, 'areaCode', MsisdnHelper::getAreaCode(data_get($service, 'msisdn', '')));
        $operation = data_get($service, 'operation');

        if ($operation === Operations::TIM_PRE_PAGO) {
            return ['id' => 'PR00460'];
        }

        return [];
    }

    public static function hasBilling($service, $customer, $address): ?array
    {
        $operation = data_get($service, 'operation');

        if ($operation === Operations::TIM_PRE_PAGO) {
            return [];
        }

        return [
            "address"       => $address,
            "billType"      => $service['billType'],
            "paymentMethod" => $service['invoiceType'],
            "dueDate"       => $service['dueDate'],
            "email"         => $customer['email'],
            "directDebit"   => array_filter([
                "bankCode" => is_null(data_get($service, 'directDebit.bankId.id'))
                    ? ''
                    : data_get($service, 'directDebit.bankId.id', ''),

                "accountNumber" => is_null(data_get($service, 'directDebit.checkingAccount'))
                    ? ''
                    : data_get($service, 'directDebit.checkingAccount', ''),

                "agencyCode" => is_null(data_get($service, 'directDebit.agency'))
                    ? ''
                    : data_get($service, 'directDebit.agency', ''),

                "operationCode" => '',
            ]),
        ];
    }

    private static function isVarejoPremiumService(Service $service): bool
    {
        $operation = data_get($service, 'operation');

        $varejoPremiumFlow = [
            Operations::TIM_BLACK,
            Operations::TIM_BLACK_EXPRESS,
            Operations::TIM_BLACK_MULTI,
            Operations::TIM_BLACK_MULTI_DEPENDENT,
            Operations::TIM_CONTROLE_FATURA,
        ];

        return in_array($operation, $varejoPremiumFlow);
    }

    private static function choiceAcceptance(Service $service): string
    {
        $operation = data_get($service, 'operation');

        if (self::isVarejoPremiumService($service)) {
            return 'CAPTURA';
        }

        if ($operation === Operations::TIM_PRE_PAGO) {
            return '';
        }

        return 'VOZ';
    }

    /** @return array[] */
    private static function getLoyalties(Service $service): array
    {
        $loyalties = data_get($service, 'loyalty.loyalties', []);

        return array_map(function ($loyalty) use ($service) {
            if (data_get($loyalty, 'type') === 'Aparelho') {
                return [
                    'id' => data_get($loyalty, 'id'),
                    'type' => data_get($loyalty, 'type'),
                    'device' => [
                        'imei' => $service->imei,
                        'stockId' => $service->device['externalIdentifier'] ?? '',
                        'device-type' => 'Aparelho',
                    ]
                ];
            }

            return [
                'id' => data_get($loyalty, 'id'),
                'type' => data_get($loyalty, 'type')
            ];
        }, $loyalties);
    }
}

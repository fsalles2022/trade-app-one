<?php

declare(strict_types=1);

namespace TimBR\Services;

use TimBR\Enumerators\TimBRSegments;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class TimBRMapOrderApprovalService
{
    /**
     * @param mixed[] $cep
     * @param mixed[] $payload
     * @return mixed[]
     */
    public static function map(Eligibility $eligibility, PointOfSale $pointOfSale, array $address, array $payload): array
    {
        return [
            'order' => [
                'smartThreadWorking' => false,
                'vendor' => [
                    'profile' => 'APPVAREJO'
                ],
                'pdv' => [
                    'custCode'          => $pointOfSale->providerIdentifiers['TIM'] ?? null,
                    'channel'           => 'VAREJO PREMIUM',
                    'name'              => $pointOfSale->label,
                    'stateCode'         => $pointOfSale->state,
                    'regionalFraude'    => '',
                    'channelFraude'     => '',
                ],
                'customer' => [
                    'contract' => [
                        'msisdn'    => '',
                        'ddd'       => data_get($payload, 'areaCode'),
                    ],

                    'customerType'      => 'PF',
                    'name'              => data_get($payload, 'customer.firstName') . ' ' . data_get($payload, 'customer.lastName'),
                    'socialSecNo'       => data_get($payload, 'customer.cpf'),
                    'ruralObligations'  => 'N',

                    'equipment' => [
                        'score' => "0"
                    ],

                    'income' => '',

                    'company' => [
                        'contract' => [
                            [
                                'socialSecNo' => ''
                            ]
                        ],
                        'foundationDate'        => '',
                        'stateResgistration'    => '',
                        'capitalShare'          => '',
                    ],

                    'birthDate'     => date('d/m/Y', strtotime(data_get($payload, 'customer.birthday'))),
                    'gender'        => data_get($payload, 'customer.gender') === 'F' ? 'F' : 'M',
                    'motherName'    => data_get($payload, 'customer.filiation'),
                    'contactNumber' => data_get($payload, 'customer.mainPhone'),
                    'email'         => data_get($payload, 'customer.email'),

                    'identityDocument' => [
                        'number'        => data_get($payload, 'customer.rg'),
                        'type'          => "RG",
                        'issueDate'     => date('d/m/Y', strtotime(data_get($payload, 'customer.rgDate'))),
                        'issuerAgency'  => data_get($payload, 'customer.rgLocal'),
                        'issuerStateCode'  => data_get($payload, 'customer.rgState'),
                    ],

                    'address' => array_filter([
                        'postalCode'   => data_get($address, 'postCode'),
                        'streetType'   => data_get($address, 'streetType'),
                        'streetName'   => data_get($address, 'streetName'),
                        'number'       => data_get($payload, 'customer.number', ''),
                        'neighborhood' => data_get($address, 'locality'),
                        'cityName'     => data_get($address, 'city'),
                        'stateCode'    => data_get($address, 'stateOrProvince'),
                        'country'      => 'Brasil',
                        'complement'   => data_get($address, 'complement', ''),
                    ])
                ],
                'assignor' => [
                    'socialSecNo' => ''
                ],
                'transactionToken' => $eligibility->eligibilityToken,
                'plan' => [
                    'id'        => data_get($payload, 'product.id'),
                    'name'      => data_get($payload, 'product.name'),
                    'segment'   => TimBRSegments::TRANSLATE[data_get($payload, 'operation')] ?? '',
                ],
                'services'  => array_filter(
                    array_merge(
                        (array) data_get($payload, 'services', []),
                        (array) data_get($payload, 'loyalties', [])
                    )
                ),
                'packages'  => array_filter((array) data_get($payload, 'packages', [])),
                'offers'    => [],
                'functionality' => [
                    'description' => self::getFunctionalityDescription($payload),
                    'detail' => self::getFunctionalityDetailsDescription($payload),
                ]
            ],
        ];
    }

    private static function getFunctionalityDescription(array $payload): string
    {
        $mode      = data_get($payload, 'mode');
        $operation = data_get($payload, 'operation');

        // Make TIM Controle Activation
        if (in_array($mode, [ Modes::ACTIVATION, Modes::PORTABILITY ]) && $operation === Operations::TIM_CONTROLE_FATURA) {
            return 'AtivarAcessoControle';
        }

        // Make TIM Controle Migration
        if ($mode === Modes::MIGRATION && $operation === Operations::TIM_CONTROLE_FATURA) {
            return 'MigrarAcessoPreControle';
        }

        // Make TIM POS Activation, except family (TIM_BLACK_MULTI)
        if (in_array($mode, [ Modes::ACTIVATION, Modes::PORTABILITY ]) && in_array($operation, [ Operations::TIM_BLACK, Operations::TIM_BLACK_EXPRESS ])) {
            return 'AtivarAcessoPos';
        }

        // Make TIM POS Migration, except family (TIM_BLACK_MULTI)
        if ($mode === Modes::MIGRATION && in_array($operation, [ Operations::TIM_BLACK, Operations::TIM_BLACK_EXPRESS ])) {
            return 'MigrarAcessoPrePos';
        }

        // Make TIM POS FAMILY Activation and Migration
        if ($operation === Operations::TIM_BLACK_MULTI) {
            return 'GrupoFamilia';
        }

        return '';
    }

    private static function getFunctionalityDetailsDescription(array $payload): array
    {
        $mode             = data_get($payload, 'mode');
        $operation        = data_get($payload, 'operation');
        $loyalties        = data_get($payload, 'loyalties');
        $hasDeviceLoyalty = data_get($payload, 'hasDeviceLoyalty');
        $details          = [];

        // When has plan loyalty
        if (! empty($loyalties) && is_array($loyalties) && count($loyalties) > 0) {
            $details[] = [
                'description' => 'FidelizacaoPlano'
            ];

            $details[] = [
                'description' => 'AtivaDesativaDesconto'
            ];
        }

        // When has device loyalty
        if ($hasDeviceLoyalty === true) {
            $details[] = [
                'description' => 'FidelizacaoAparelho'
            ];
        }

        // When operation is POS FAMILY
        if ($operation === Operations::TIM_BLACK_MULTI) {
            $details[] = [
                'description' => 'AtivarAcessoFamilia'
            ];
        }

        // When mode is PORTABILITY
        if ($mode === Modes::PORTABILITY) {
            $details[] = [
                'description' => 'Portabilidade'
            ];
        }

        // When details is empty, set default value
        if (empty($details)) {
            $details[] = [
                'description' => ''
            ];
        }

        return $details;
    }
}

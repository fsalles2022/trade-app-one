<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests;

use TradeAppOne\Domain\Enumerators\Modes;

final class Siv3TestBook
{
    public const DEFAULT_URI                  = 'https://siv3.test';
    public const NON_EXISTENT_CUSTOMER_SALE   = '04155519194';
    public const EXISTENT_CUSTOMER_SALE       = '25481523086';
    public const MSISDN_FAILURE               = '11953523814';
    public const MSISDN_SUCCESS               = '11953555813';
    public const SUCCESS_POSTAL_CODE          = '06540-080';
    public const SUCCESS_ADDRESS_ID           = 1;
    public const SUCCESS_CPF_CREDIT           = '343.322.222-45';
    public const PHONE_NUMBER_SUCCESS         = '+5511999999999';
    public const CODE_AUTHORIZATION_SUCCESS   = 'abc';
    public const CODE_AUTHORIZATION_EXCEPTION = 'zxc';

    public const ZIP_CODE_NOT_FOUND_ADDRESS = '02524-000';

    public const DATE_SUCESS_EXPORT = [
        'startDate' => '12-05-2021 00:00:00',
        'endDate' => '06-05-2021 00:00:00'
    ];

    public const DATE_FAILURE_EXPORT = [
        'startDate' => '11-11-2111 00:00:00',
        'endDate' => '11-11-2111 00:00:00'
    ];

    public const AUTH_SIV3_CREDENTIALS = [
        'login' => 'user@example.com',
        'password' => '123'
    ];

    public const SALE_NON_EXISTENT = [
        'saleExists' => false,
        'saleId' => 0
    ];

    public const SALE_EXISTENT = [
        'saleExists' => true,
        'saleId' => 102030
    ];

    public const SALE_CREATED = [
        'success' => true,
        'saleId' => 102099
    ];

    public const SALE_NOT_CREATED = [
        'success' => false,
        'saleId' => 0
    ];

    public const SALES_EXPORTABLE = [
        'data' => [
            [
                'mode' => Modes::ACTIVATION,
                'areaCode' => 11,
                'msisdn' => '991542343',
                'iccid' => '89550000000000000000',
                'customerCpf' => '04155519199',
                'customerEmail' => 'teste@teste.com',
                'salesmanCpf' => '99911133323',
                'salesmanName' => 'Teste',
                'salesmanAreaCode' => '11',
                'pointOfSaleCode' => 'XPTO',
                'pointOfSaleHierarchyId' => '1',
                'pointOfSaleHierarchyName' => 'Trade Up Group',
                'pointOfSaleName' => 'XPTO',
                'networkSlug' => 'Claro',
                'createdAt' => '2022-09-20 09:46:05'
            ],
            [
                'mode' => Modes::PORTABILITY,
                'areaCode' => 21,
                'msisdn' => '991549871',
                'iccid' => '89550000000000000001',
                'customerCpf' => '33344455567',
                'customerEmail' => 'teste@teste.com',
                'salesmanCpf' => '99988877766',
                'salesmanName' => 'Teste',
                'salesmanAreaCode' => '11',
                'pointOfSaleCode' => 'V39V',
                'pointOfSaleHierarchyId' => '1',
                'pointOfSaleHierarchyName' => 'Trade Up Group',
                'pointOfSaleName' => 'XPTO',
                'networkSlug' => 'Claro',
                'createdAt' => '2022-09-14 17:42:47'
            ],
            [
                'mode' => Modes::ACTIVATION,
                'areaCode' => 51,
                'msisdn' => '984321122',
                'iccid' => '89550000000000000002',
                'customerCpf' => '55544433322',
                'customerEmail' => 'teste@teste.com',
                'salesmanCpf' => '22233344455',
                'salesmanName' => 'Teste',
                'salesmanAreaCode' => '11',
                'pointOfSaleCode' => 'H2T3',
                'pointOfSaleHierarchyId' => '1',
                'pointOfSaleHierarchyName' => 'Trade Up Group',
                'pointOfSaleName' => 'XPTO',
                'networkSlug' => 'Claro',
                'createdAt' => '2022-09-19 13:31:08'
            ]
        ]
    ];

    public const NON_EXISTENTS_SALES_EXPORTABLE = [
        'data' => []
    ];

    public const SUCCESS_EXTERNAL_SALE = [
        'areaCode' => '11',
        'customerCpf' => Siv3TestBook::NON_EXISTENT_CUSTOMER_SALE,
        'iccid' => '89550000000000000000',
        'msisdn' => Siv3TestBook::MSISDN_SUCCESS,
        'mode' => Modes::ACTIVATION,
        'operation' => 'CLARO_PRE_EXTERNAL_SALE'
    ];

    public const FAILURE_EXTERNAL_SALE = [
        'areaCode' => '11',
        'customerCpf' => Siv3TestBook::EXISTENT_CUSTOMER_SALE,
        'iccid' => '89550000000000000000',
        'msisdn' => Siv3TestBook::MSISDN_FAILURE,
        'mode' => Modes::ACTIVATION,
        'operation' => 'CLARO_PRE_EXTERNAL_SALE'
    ];

    public const REPORT_EXTERNAL_SALE_FILTERS_SUCCESS = [
        'network' => [
            'tradeup-group'
        ],

        'pointsofsale' => [
            15811365000173
        ],

        'status' => [
            'ACCEPTED'
        ],
    ];

    public const REPORT_EXTERNAL_SALE_FILTERS_FAILURE = [
        'network' => [
            'via-varejo'
        ],

        'pointsofsale' => [
            00000000000000
        ],

        'status' => [
            'CANCELED'
        ],
    ];

    /** @var mixed[] */
    public const SUCCESS_ADDRESS_BY_CPF = [
        [
            'id'=> 1,
            'address'=> 'Rua Dona Rosalina',
            'addressAbreviature'=> 'Rua Dona Rosalina',
            'neighborhood'=> 'Jardim das Belezas',
            'cityId'=> 5533,
            'city'=> 'Carapicuíba',
            'cityIdExternal'=> '5533',
            'operatorCode'=> '533',
            'stateAcronym'=> 'SP',
            'postalCode'=> '06322030'
        ]
    ];

    /** @var mixed[] */
    public const FAILURE_ADDRESS_BY_CPF = [
        'error' => true,
        'message' => 'Endereço não foi encontrado, ou não atende as especificações. CEP utilizado foi: 54709120'
    ];

    /** @var mixed[] */
    public const SUCCESS_VIABILITY = [
        'hps'=> [
            [
                'id'=> 641665483,
                'hpAddress'=> [
                    'id'=> null,
                    'address'=> 'BENEDITO SERBINO',
                    'postalCode'=> '12929160',
                    'number'=> '16',
                    'complement'=> 'CASA 2',
                    'neighborhood'=> 'SANTO AMARO',
                    'cityId'=> 5474,
                    'city'=> 'BRAGANCA PAULISTA',
                    'state'=> 'SP'
                ],
                'address'=> [
                    'id'=> 3,
                    'streetId'=> 957,
                    'streetName'=> 'JARDIM AGUAS CLARAS',
                    'postalCode'=> '12929160',
                    'neighborhoodId'=> 26491,
                    'neighborhood'=> 'SANTO AMARO',
                    'cityId'=> 5474,
                    'city'=> 'BRAGANCA PAULISTA',
                    'stateId'=> 'SP',
                    'stateAcronym'=> 'SP'
                ]
            ]
        ]
    ];

    /** @var string[] */
    public const SUCCESS_RESIDENTIL_CREDIT_ANALYSIS = [
        'status'=> 'Aprovado',
        'systemStatus'=> 'approved',
        'statusDescription'=> 'APROVADO - CONFORME POLITICA DE CREDITO EM VIGOR   LIMITE SUGERIDO=> R$ 1000,00',
        'externalTransaction'=> '552dd1b2-30d9-41e2-9234-bc6480dacdfa',
        'document'=> '343.322.222-45',
        'name'=> 'Usuario SandBox de teste'
    ];
}

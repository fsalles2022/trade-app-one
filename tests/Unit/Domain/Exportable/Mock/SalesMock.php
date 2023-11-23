<?php

declare(strict_types=1);

namespace TradeAppOne\Tests\Unit\Domain\Exportable\Mock;

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use TradeAppOne\Domain\Models\Collections\Sale;

class SalesMock
{
    /** @return Sale[] */
    public static function get(): array
    {
        $sale = new Sale();

        $sale->forceFill([
            'user' => [
                'id'                     => 9999,
                'firstName'              => 'ZÉ',
                'lastName'               => 'Santos',
                'cpf'                    => '12345678901',
                'email'                  => '12345678901@email.com',
                'areaCode'               => null,
                'integrationCredentials' => null,
                'role'                   => 'vendedor',
                'associative'            => [
                    'attributes' => [],
                    'request'    => [],
                    'query'      => [],
                    'server'     => [],
                    'files'      => [],
                    'cookies'    => [],
                    'headers'    => [],
                ],
            ],
            'userAlternate' => null,
            'pointOfSale'   => [
                'id'                  => 99999,
                'slug'                => '123',
                'label'               => 'Loja',
                'cnpj'                => '23456677777777',
                'tradingName'         => 'Loja 1',
                'companyName'         => 'LOJAS ZEQUINHAS',
                'providerIdentifiers' => [
                    'OI'    => '33333',
                    'TIM'   => 'CS60_111A_AAABBB_A002',
                    'CLARO' => 'AAAA',
                ],
                'state'     => 'GO',
                'city'      => 'AN¡POLIS',
                'areaCode'  => '62',
                'hierarchy' => [
                    'id'       => 358,
                    'slug'     => 'loja-do-zequinhas',
                    'label'    => 'Loja-do-Zequinhas',
                    'sequence' => '1.4.16',
                ],
                'network' => [
                    'id'                => 6,
                    'slug'              => 'Zequinhas',
                    'label'             => 'Zequinhas',
                    'cnpj'              => '00000000000000',
                    'companyName'       => 'Zequinhas',
                    'availableServices' => null,
                ],
            ],
            'channel'         => 'VAREJO',
            'source'          => 'WEB',
            'saleTransaction' => '222222222222',
            'services'        => [
                [
                    'operator'    => 'CLARO',
                    'operation'   => 'CONTROLE_BOLETO',
                    'product'     => 123,
                    'dueDate'     => 29,
                    'iccid'       => '9999999999999999990',
                    'areaCode'    => '62',
                    'invoiceType' => 'VIA_POSTAL',
                    'promotion'   => [
                    'label'   => 'Controle Conectado 8GB - Fidel - 89479',
                    'price'   => -10.0,
                    'product' => 606,
                    ],
                    'mode'              => 'ACTIVATION',
                    'isPreSale'         => false,
                    'hasRecommendation' => false,
                    'recommendation'    => [
                        'registration' => null,
                        'name'         => null,
                    ],
                    'customer' => [
                        'cpf'            => '12343213212',
                        'firstName'      => 'Mariazinha',
                        'lastName'       => 'Santos',
                        'email'          => 'm  ariazinha@email.com',
                        'gender'         => 'F',
                        'birthday'       => '1946-11-09',
                        'filiation'      => 'Joaquinha Santos',
                        'mainPhone'      => '+5597766554477',
                        'secondaryPhone' => '+5521981133099',
                        'rg'             => '99988877',
                        'rgDate'         => '2023-03-22',
                        'rgLocal'        => 'SSP',
                        'rgState'        => 'GO',
                        "witnessRg1"     => '01001001001',
                        "witnessName1"   => 'João da Silva',
                        "witnessRg2"     => '10101010101',
                        "witnessName2"   => 'Maria Joaquina',
                        'zipCode'        => '1112223',
                        'local'          => 'AV Teste',
                        'localId'        => 'AVENIDA',
                        'state'          => 'GO',
                        'city'           => 'Anápolis',
                        'neighborhood'   => 'Interlândia',
                        'number'         => '9999',
                        'complement'     => 'Rua Alameda de Teste',
                    ],
                    'sector'              => 'TELECOMMUNICATION',
                    'status'              => 'APPROVED',
                    'label'               => 'Controle 8GB - Conectado',
                    'price'               => 49.99,
                    'serviceTransaction'  => '987654433345676-0',
                    '_id'                 => new ObjectId('6079e1652b0a9ffdde4c49cf'),
                    'operatorIdentifiers' => [
                    'venda_id'   => 1234555,
                    'servico_id' => 12334455,
                    'acceptance' => 'adawdawdawd123',
                    ],
                    'updatedAt' => new UTCDateTime(1614614729000),
                    'log'       => [
                        [
                            'type'    => 'success',
                            'message' => 'Por gentileza, escolha um telefone',
                            'data'    => [
                                'msisdns' => [
                                    0 => '12312312312',
                                    1 => '12312312312',
                                    2 => '12312321333',
                                    3 => '12312321322',
                                    4 => '12312333333',
                                ],
                            ],
                            'support' => 'choose_phone',
                        ],
                        [
                            'type'    => 'success',
                            'message' => 'Serviço ativado com sucesso',
                            'data'    => [
                            'protocol'           => '1231211111',
                            'status'             => 'SUCESSO',
                            'authorization_code' => '12111111',
                            'ea_ticket'          => null,
                            'msisdn'             => '+55123123123',
                            ],
                            'send_sale_to_inova' => [
                            'type'     => 'error',
                            'response' => 'Usuário precisa ser do canal de distrubuição para prosseguir.',
                            ],
                        ],
                    ],
                    'msisdn'           => '12312312312',
                    'statusThirdParty' => 'APROVADO',
                ],
            ],
            'total'     => 49.99,
            'updatedAt' => '2021-03-01 13:05:29',
            'createdAt' => '2021-03-01 12:31:48',
        ]);

        return [
            $sale
        ];
    }
}

<?php

namespace Reports\Tests\Unit\Exportables\MobileApplications;

class MobileApplicationExportFixture
{
    public static function fixture()
    {
        return data_get(self::fixtureFromElastic(), 'hits.hits');
    }

    public static function fixtureFromElastic()
    {
        return array(
            'took'      => 9,
            'timed_out' => false,
            '_shards'   =>
                array(
                    'total'      => 5,
                    'successful' => 5,
                    'skipped'    => 0,
                    'failed'     => 0,
                ),
            'hits'      =>
                array(
                    'total'     => 701,
                    'max_score' => 1,
                    'hits'      => [
                        [
                            '_index'  => 'report',
                            '_type'   => 'doc',
                            '_id'     => '2018102716342665-0',
                            '_score'  => 7.55545660000000030009914553374983370304107666015625,
                            '_source' => [
                                'channel'     => 'WEB',

                                'created_at'     => '2018-10-27T19:26:39.000Z',
                                'updated_at'     => '2018-11-13T16:50:01.000Z',
                                'service_status' => 'APPROVED',

                                'pointofsale_id'                  => 315,
                                'pointofsale_city'                => 'BELO HORIZONTE',
                                'pointofsale_tradingname'         => 'IPLACE',
                                'pointofsale_cnpj'                => '00000000000000',
                                'pointofsale_network_cnpj'        => '00000000000000',
                                'pointofsale_label'               => 'Iplace - 609',
                                'pointofsale_slug'                => '609',
                                'pointofsale_companyname'         => 'IPLACE MOBILE',
                                'pointofsale_hierarchy_id'        => 18,
                                'pointofsale_network_id'          => 4,
                                'pointofsale_areacode'            => '31',
                                'pointofsale_network_companyname' => 'Iplace',
                                'pointofsale_network_label'       => 'Iplace',
                                'pointofsale_state'               => 'MG',
                                'pointofsale_hierarchy_label'     => 'Regional 2',
                                'pointofsale_hierarchy_sequence'  => '1.2.1.2.1',
                                'pointofsale_network_slug'        => 'iplace',

                                'user_id'        => 5320,
                                'user_cpf'       => '0000000001',
                                'user_firstname' => 'JOAO',
                                'user_lastname'  => 'JOAO MORENO',
                                'user_email'     => '9593805664@iplace.com.br',
                                'user_role'      => 'vendedor-iplace',

                                'service_servicetransaction' => '2018102716342665-0',
                                'service_operator'           => 'CLARO',
                                'service_operation'          => 'CLARO_POS',
                                'service_price'              => 149.99,
                                'service_msisdn'             => '+5531999999999',
                                'service_label'              => 'Claro Pós 10GB',
                                'service_mode'               => 'ACTIVATION',

                                'service_customer_cpf'            => '0000000000',
                                'service_customer_lastname'       => 'DAS NEVES',
                                'service_customer_firstname'      => 'DAS NEVES',
                                'service_customer_state'          => 'MG',
                                'service_customer_zipcode'        => '231230000',
                                'service_customer_mainphone'      => '+553199999999',
                                'service_customer_local'          => 'JOANA DARK',
                                'service_customer_complement'     => 'CASA',
                                'service_customer_rg'             => '239123123',
                                'service_customer_neighborhood'   => 'INCONFIDENTES',
                                'service_customer_city'           => 'Ouro Branco',
                                'service_customer_gender'         => 'F',
                                'service_customer_filiation'      => 'JOANA',
                                'service_customer_secondaryphone' => '+5531999999',

                                'service_imei'           => '356134091815768',
                                'service_sector'         => 'TELECOMMUNICATION',
                                'service_product'        => 51,
                                'service_device_id'      => 3,
                                'service_device_model'   => 'iPhone X',
                                'service_device_storage' => '32GB',
                                'service_device_color'   => 'Azul',

                                'service_evaluations_salesman_price'     => '23',
                                'service_evaluations_salesman_createdAt' => '2018-12-13T16:50:01.000Z',

                                'service_evaluations_appraiser_price'     => '34',
                                'service_evaluations_appraiser_createdAt' => '2018-11-13T16:50:01.000Z',

                                'service_updatedat' => '2018-10-27T19:40:18.000Z',
                                '@version'          => '1',
                                '@timestamp'        => '2018-12-06T17:01:49.937Z',
                            ],
                        ]
                    ],
                ),
        );
    }
}

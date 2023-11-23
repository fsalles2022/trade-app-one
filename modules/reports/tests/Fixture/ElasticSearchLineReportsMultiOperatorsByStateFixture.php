<?php

namespace Reports\Tests\Fixture;

class ElasticSearchLineReportsMultiOperatorsByStateFixture
{
    public static function getSaleArray()
    {
        return array(
            'took' => 125,
            'timed_out' => false,
            '_shards' =>
                array(
                    'total' => 5,
                    'successful' => 5,
                    'skipped' => 0,
                    'failed' => 0,
                ),
            'hits' =>
                array(
                    'total' => 146291,
                    'max_score' => 8.57059,
                    'hits' =>
                        array(
                            0 =>
                                array(
                                    '_index' => 'tao',
                                    '_type' => 'doc',
                                    '_id' => '201905232041352428-0',
                                    '_score' => 8.57059,
                                    '_source' =>
                                        array(
                                            '@version' => '1',
                                            'service_status' => 'ACCEPTED',
                                            'pointofsale_hierarchy_sequence' => '1.4.2.7',
                                            'pointofsale_hierarchy_label' => 'Regional CTB',
                                            'pointofsale_hierarchy_slug' => 'riachuelo-regional-ctb',
                                            'pointofsale_tradingname' => 'RIACHUELO',
                                            'pointofsale_hierarchy_id' => 73,
                                            'user_email' => '10736739955@riachuelo.com',
                                            'pointofsale_label' => 'L054',
                                            'service_customer_city' => 'Almirante Tamandaré',
                                            'service_customer_firstname' => 'Bruno',
                                            'service_msisdn' => '41985081437',
                                            '@timestamp' => '2019-05-27T13:06:17.078Z',
                                            'service_mode' => 'MIGRATION',
                                            'service_statusthirdparty' => 'PendenteRecargaAvulsa',
                                            'pointofsale_network_slug' => 'riachuelo',
                                            'pointofsale_areacode' => '41',
                                            'service_customer_zipcode' => '83507590',
                                            'user_cpf' => '10736739955',
                                            'pointofsale_cnpj' => '33200056002788',
                                            'user_firstname' => 'LUCCA',
                                            'pointofsale_provideridentifiers_oi' => '1035812',
                                            'pointofsale_provideridentifiers_nextel_cod' => '-',
                                            'updated_at' => '2019-05-23T23:41:36.000Z',
                                            'pointofsale_provideridentifiers_tim' => 'TS40_TSAGCI_TS2876_A002',
                                            'service_customer_mainphone' => '+5541995854324',
                                            'service_price' => 39.82,
                                            'service_customer_number' => '99',
                                            'pointofsale_companyname' => 'LOJAS RIACHUELO',
                                            'service_operation' => 'OI_CONTROLE_BOLETO',
                                            'pointofsale_city' => 'CURITIBA',
                                            'service_customer_complement' => 'Casa',
                                            'service_customer_lastname' => 'Brusaolin',
                                            'pointofsale_network_cnpj' => '00000000000002',
                                            'pointofsale_network_label' => 'Riachuelo',
                                            'service_customer_neighborhood' => 'Lamenha Grande',
                                            'service_sector' => 'TELECOMMUNICATION',
                                            'service_label' => 'B - Oi Mais Controle Intermediário G3 - R$39,99',
                                            'user_id' => 10989,
                                            'service_customer_state' => 'PR',
                                            'service_operator' => 'OI',
                                            'service_product' => 'OCSF114',
                                            'service_customer_local' => 'Rua Luiz Bugalski',
                                            'created_at' => '2019-05-23T23:41:36.000Z',
                                            'pointofsale_state' => 'PR',
                                            'service_imei' => '354143106477938',
                                            'pointofsale_provideridentifiers_claro' => 'AMW0',
                                            'pointofsale_provideridentifiers_nextel_ref' => '-',
                                            'pointofsale_network_companyname' => 'Riachuelo',
                                            'user_role' => 'vendedor-riachuelo',
                                            'pointofsale_network_id' => 6,
                                            'service_valueadhesion' => 34.75,
                                            'pointofsale_id' => 604,
                                            'pointofsale_slug' => '54',
                                            'service_customer_cpf' => '05233414902',
                                            'service_servicetransaction' => '201905232041352428-0',
                                            'user_lastname' => 'VINICIOS BRUM DOS SANTOS',
                                        ),
                                ),
                            1 =>
                                array(
                                    '_index' => 'tao',
                                    '_type' => 'doc',
                                    '_id' => '201905241358359890-0',
                                    '_score' => 8.57059,
                                    '_source' =>
                                        array(
                                            '@version' => '1',
                                            'service_status' => 'ACCEPTED',
                                            'pointofsale_hierarchy_sequence' => '1.7',
                                            'pointofsale_hierarchy_label' => 'Schumann',
                                            'pointofsale_hierarchy_slug' => 'rede-schumann',
                                            'pointofsale_tradingname' => 'SCHUMANN',
                                            'pointofsale_hierarchy_id' => 147,
                                            'user_email' => '09036561914@schumann.com',
                                            'pointofsale_label' => '003',
                                            'service_customer_city' => 'Seara',
                                            'service_customer_firstname' => 'Orides Soares',
                                            'service_msisdn' => '49984366257',
                                            '@timestamp' => '2019-05-27T13:06:17.105Z',
                                            'service_mode' => 'MIGRATION',
                                            'service_statusthirdparty' => 'PendenteRecargaAvulsa',
                                            'pointofsale_network_slug' => 'schumann',
                                            'pointofsale_areacode' => '049',
                                            'service_customer_zipcode' => '89770000',
                                            'user_cpf' => '09036561914',
                                            'pointofsale_cnpj' => '02158816000840',
                                            'user_firstname' => 'LEONARDO',
                                            'pointofsale_provideridentifiers_oi' => '1018418',
                                            'updated_at' => '2019-05-24T16:58:36.000Z',
                                            'pointofsale_provideridentifiers_tim' => 'TS40_TSAGCI_TS0318_A002',
                                            'service_customer_mainphone' => '+5549984366257',
                                            'service_price' => 39.82,
                                            'service_customer_number' => '12',
                                            'pointofsale_companyname' => 'SCHUMANN',
                                            'service_operation' => 'OI_CONTROLE_BOLETO',
                                            'pointofsale_city' => 'SEARA',
                                            'service_customer_lastname' => 'De Lima',
                                            'pointofsale_network_cnpj' => '00000000000008',
                                            'pointofsale_network_label' => 'Schumann',
                                            'service_customer_neighborhood' => 'Interior',
                                            'service_sector' => 'TELECOMMUNICATION',
                                            'service_label' => 'B - Oi Mais Controle Intermediário G3 - R$39,99',
                                            'user_id' => 33696,
                                            'service_customer_state' => 'SC',
                                            'service_operator' => 'OI',
                                            'service_product' => 'OCSF114',
                                            'service_customer_local' => 'Linha salete',
                                            'created_at' => '2019-05-24T16:58:36.000Z',
                                            'pointofsale_state' => 'SC',
                                            'service_imei' => '353690104953949',
                                            'pointofsale_provideridentifiers_claro' => 'AK1O',
                                            'pointofsale_network_companyname' => 'Schumann',
                                            'user_role' => 'vendedor-schumann',
                                            'pointofsale_network_id' => 12,
                                            'service_valueadhesion' => 34.75,
                                            'pointofsale_id' => 1476,
                                            'pointofsale_slug' => '003',
                                            'service_customer_cpf' => '79888755900',
                                            'service_servicetransaction' => '201905241358359890-0',
                                            'user_lastname' => 'GABRIEL PRIOR',
                                        ),
                                ),
                            2 =>
                                array(
                                    '_index' => 'tao',
                                    '_type' => 'doc',
                                    '_id' => '201905231312236356-0',
                                    '_score' => 8.57059,
                                    '_source' =>
                                        array(
                                            '@version' => '1',
                                            'service_status' => 'ACCEPTED',
                                            'pointofsale_hierarchy_sequence' => '1.3.11',
                                            'pointofsale_hierarchy_label' => 'Regional 11',
                                            'pointofsale_hierarchy_slug' => 'pernambucanas-regional11',
                                            'pointofsale_tradingname' => 'PERNAMBUCANAS',
                                            'pointofsale_hierarchy_id' => 51,
                                            'user_email' => '03903912204@pernambucanas.com',
                                            'pointofsale_label' => 'LOJA433',
                                            'service_customer_city' => 'Lucas do Rio Verde',
                                            'service_customer_firstname' => 'luciana candida',
                                            'service_msisdn' => '65984746122',
                                            '@timestamp' => '2019-05-27T13:06:17.157Z',
                                            'service_mode' => 'MIGRATION',
                                            'service_statusthirdparty' => 'PendenteRecargaAvulsa',
                                            'pointofsale_network_slug' => 'pernambucanas',
                                            'pointofsale_areacode' => '65',
                                            'service_customer_zipcode' => '78455000',
                                            'user_cpf' => '03903912204',
                                            'pointofsale_cnpj' => '61099834068232',
                                            'user_firstname' => 'VICTOR',
                                            'pointofsale_provideridentifiers_oi' => '1044450',
                                            'updated_at' => '2019-05-23T16:12:24.000Z',
                                            'pointofsale_provideridentifiers_tim' => 'CS60_MGMATI_VA1889_A003',
                                            'service_customer_mainphone' => '+5565996335292',
                                            'service_price' => 39.82,
                                            'service_customer_number' => '1222',
                                            'pointofsale_companyname' => '',
                                            'service_operation' => 'OI_CONTROLE_BOLETO',
                                            'pointofsale_city' => 'LUCAS DO RIO VERDE',
                                            'service_customer_complement' => 'w',
                                            'service_customer_lastname' => 'de jesus',
                                            'pointofsale_network_cnpj' => '00000000000003',
                                            'pointofsale_network_label' => 'Pernambucanas',
                                            'service_customer_neighborhood' => 'parque das emas',
                                            'service_sector' => 'TELECOMMUNICATION',
                                            'service_label' => 'B - Oi Mais Controle Intermediário G3 - R$39,99',
                                            'user_id' => 13646,
                                            'service_customer_state' => 'MT',
                                            'service_operator' => 'OI',
                                            'service_product' => 'OCSF114',
                                            'service_customer_local' => 'rua das calopsitas',
                                            'created_at' => '2019-05-23T16:12:24.000Z',
                                            'pointofsale_state' => 'MT',
                                            'pointofsale_provideridentifiers_claro' => '6WQD',
                                            'pointofsale_network_companyname' => 'Pernambucanas',
                                            'user_role' => 'vendedor-pernambucanas',
                                            'pointofsale_network_id' => 7,
                                            'service_valueadhesion' => 34.75,
                                            'pointofsale_id' => 978,
                                            'pointofsale_slug' => '433',
                                            'service_customer_cpf' => '94831840106',
                                            'service_servicetransaction' => '201905231312236356-0',
                                            'user_lastname' => 'TRINDADE',
                                        ),
                                ),
                            3 =>
                                array(
                                    '_index' => 'tao',
                                    '_type' => 'doc',
                                    '_id' => '201905231510113511-1',
                                    '_score' => 8.57059,
                                    '_source' =>
                                        array(
                                            '@version' => '1',
                                            'service_status' => 'ACCEPTED',
                                            'pointofsale_tradingname' => 'PERNAMBUCANAS',
                                            'user_email' => '44011118850@pernambucanas.com',
                                            'pointofsale_label' => 'LOJA53',
                                            'service_customer_city' => 'Santo André',
                                            'service_customer_firstname' => 'fabio',
                                            'service_msisdn' => '11967062179',
                                            '@timestamp' => '2019-05-27T13:06:17.327Z',
                                            'service_mode' => 'MIGRATION',
                                            'service_statusthirdparty' => 'PendenteRecargaAvulsa',
                                            'pointofsale_network_slug' => 'pernambucanas',
                                            'pointofsale_areacode' => '',
                                            'service_customer_zipcode' => '09250300',
                                            'user_cpf' => '44011118850',
                                            'pointofsale_cnpj' => '61099834070725',
                                            'user_firstname' => 'BRUNO',
                                            'pointofsale_provideridentifiers_oi' => '1047171',
                                            'updated_at' => '2019-05-23T18:10:12.000Z',
                                            'pointofsale_provideridentifiers_tim' => 'SP10_MGVALI_VA0006_A026',
                                            'service_customer_mainphone' => '+5511977058124',
                                            'service_price' => 44.9,
                                            'service_customer_number' => '153',
                                            'pointofsale_companyname' => 'PERNAMBUCANAS',
                                            'service_operation' => 'OI_CONTROLE_BOLETO',
                                            'pointofsale_city' => 'SANTO ANDRE',
                                            'service_customer_complement' => 'casa',
                                            'service_customer_lastname' => 'henrique consentino',
                                            'pointofsale_network_cnpj' => '00000000000003',
                                            'pointofsale_network_label' => 'Pernambucanas',
                                            'service_customer_neighborhood' => 'Jardim Utinga',
                                            'service_sector' => 'TELECOMMUNICATION',
                                            'service_label' => 'B - Oi Mais Controle Intermediário G2 R$44,99',
                                            'user_id' => 33243,
                                            'service_customer_state' => 'SP',
                                            'service_operator' => 'OI',
                                            'service_product' => 'OCSF115',
                                            'service_customer_local' => 'Avenida Sapopemba',
                                            'created_at' => '2019-05-23T18:10:12.000Z',
                                            'pointofsale_state' => 'SP',
                                            'pointofsale_provideridentifiers_claro' => 'SGN1',
                                            'pointofsale_network_companyname' => 'Pernambucanas',
                                            'user_role' => 'vendedor-pernambucanas',
                                            'pointofsale_network_id' => 7,
                                            'service_valueadhesion' => 39.8,
                                            'pointofsale_id' => 1627,
                                            'pointofsale_slug' => '53',
                                            'service_customer_cpf' => '28994842810',
                                            'service_servicetransaction' => '201905231510113511-1',
                                            'user_lastname' => 'NICOLUSSI',
                                        ),
                                ),
                            4 =>
                                array(
                                    '_index' => 'tao',
                                    '_type' => 'doc',
                                    '_id' => '201905241432296914-0',
                                    '_score' => 8.57059,
                                    '_source' =>
                                        array(
                                            '@version' => '1',
                                            'service_status' => 'ACCEPTED',
                                            'pointofsale_hierarchy_sequence' => '1.3.2',
                                            'pointofsale_hierarchy_label' => 'Regional 2',
                                            'pointofsale_hierarchy_slug' => 'pernambucanas-regional2',
                                            'pointofsale_tradingname' => 'PERNAMBUCANAS',
                                            'pointofsale_hierarchy_id' => 42,
                                            'user_email' => '40004977823@pernambucanas.com',
                                            'pointofsale_label' => 'LOJA130',
                                            'service_customer_city' => 'Jacareí',
                                            'service_customer_firstname' => 'Marlene',
                                            'service_msisdn' => '12988520218',
                                            '@timestamp' => '2019-05-27T13:06:17.328Z',
                                            'service_mode' => 'MIGRATION',
                                            'service_statusthirdparty' => 'PendenteRecargaAvulsa',
                                            'pointofsale_network_slug' => 'pernambucanas',
                                            'pointofsale_areacode' => '12',
                                            'service_customer_zipcode' => '12327550',
                                            'user_cpf' => '40004977823',
                                            'pointofsale_cnpj' => '61099834011800',
                                            'user_firstname' => 'ALICIANE',
                                            'pointofsale_provideridentifiers_oi' => '1011842',
                                            'pointofsale_provideridentifiers_nextel_cod' => '17630',
                                            'updated_at' => '2019-05-24T17:32:30.000Z',
                                            'pointofsale_provideridentifiers_tim' => 'SP10_MGVALI_VA0006_A008',
                                            'service_customer_mainphone' => '+5512988520218',
                                            'service_price' => 44.9,
                                            'service_customer_number' => '91',
                                            'pointofsale_companyname' => '',
                                            'service_operation' => 'OI_CONTROLE_BOLETO',
                                            'pointofsale_city' => 'JACAREI',
                                            'service_customer_lastname' => 'Ferreira bueno',
                                            'pointofsale_network_cnpj' => '00000000000003',
                                            'pointofsale_network_label' => 'Pernambucanas',
                                            'service_customer_neighborhood' => 'Jardim Paraíba',
                                            'service_sector' => 'TELECOMMUNICATION',
                                            'service_label' => 'B - Oi Mais Controle Intermediário G2 R$44,99',
                                            'user_id' => 27717,
                                            'service_customer_state' => 'SP',
                                            'service_operator' => 'OI',
                                            'service_product' => 'OCSF115',
                                            'service_customer_local' => 'Rua Valentin Pinheiro',
                                            'created_at' => '2019-05-24T17:32:30.000Z',
                                            'pointofsale_state' => 'SP',
                                            'pointofsale_provideridentifiers_claro' => 'CGXP',
                                            'pointofsale_provideridentifiers_nextel_ref' => '71842',
                                            'pointofsale_network_companyname' => 'Pernambucanas',
                                            'user_role' => 'vendedor-pernambucanas',
                                            'pointofsale_network_id' => 7,
                                            'service_valueadhesion' => 39.8,
                                            'pointofsale_id' => 890,
                                            'pointofsale_slug' => '130',
                                            'service_customer_cpf' => '39393748810',
                                            'service_servicetransaction' => '201905241432296914-0',
                                            'user_lastname' => 'SILVA',
                                        ),
                                ),
                        ),
                ),
            'aggregations' =>
                array(
                    'state_count' =>
                        array(
                            'doc_count_error_upper_bound' => 2198,
                            'sum_other_doc_count' => 71592,
                            'buckets' =>
                                array(
                                    0 =>
                                        array(
                                            'key' => 'SP',
                                            'doc_count' => 34718,
                                            'operator_count' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 15489,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 7689,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 5227,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 2432,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_POS',
                                                                                            'doc_count' => 141,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 7344,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 3699,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 3552,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'TIM_PRE_PAGO',
                                                                                            'doc_count' => 93,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 6732,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 2447,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 2230,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 2042,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_POS_PAGO',
                                                                                            'doc_count' => 13,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'NEXTEL',
                                                                    'doc_count' => 3079,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'NEXTEL_CONTROLE_BOLETO',
                                                                                            'doc_count' => 2925,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'NEXTEL_CONTROLE_CARTAO',
                                                                                            'doc_count' => 154,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            4 =>
                                                                array(
                                                                    'key' => 'OI',
                                                                    'doc_count' => 2074,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 1544,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 530,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    1 =>
                                        array(
                                            'key' => 'PR',
                                            'doc_count' => 11438,
                                            'operator_count' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 5746,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 3346,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 1297,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 1099,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_POS',
                                                                                            'doc_count' => 4,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 2915,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 1997,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 835,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'TIM_PRE_PAGO',
                                                                                            'doc_count' => 83,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 1886,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 847,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 652,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 387,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'OI',
                                                                    'doc_count' => 891,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 588,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 303,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    2 =>
                                        array(
                                            'key' => 'RS',
                                            'doc_count' => 10125,
                                            'operator_count' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 6667,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 4060,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 2376,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 187,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_POS',
                                                                                            'doc_count' => 44,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 2298,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 1119,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 1080,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 97,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_POS_PAGO',
                                                                                            'doc_count' => 2,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 885,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 728,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 144,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'TIM_PRE_PAGO',
                                                                                            'doc_count' => 13,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'OI',
                                                                    'doc_count' => 275,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 180,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 95,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    3 =>
                                        array(
                                            'key' => 'MG',
                                            'doc_count' => 10008,
                                            'operator_count' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 3800,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 2174,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 1395,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 225,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_INTERNET_MOVEL_POS',
                                                                                            'doc_count' => 3,
                                                                                        ),
                                                                                    4 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_POS_PAGO',
                                                                                            'doc_count' => 3,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 3650,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 1469,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 1314,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 835,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_POS',
                                                                                            'doc_count' => 32,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 2110,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 1101,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 982,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'TIM_PRE_PAGO',
                                                                                            'doc_count' => 27,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'OI',
                                                                    'doc_count' => 448,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 246,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 202,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                    4 =>
                                        array(
                                            'key' => 'PA',
                                            'doc_count' => 8410,
                                            'operator_count' =>
                                                array(
                                                    'doc_count_error_upper_bound' => 0,
                                                    'sum_other_doc_count' => 0,
                                                    'buckets' =>
                                                        array(
                                                            0 =>
                                                                array(
                                                                    'key' => 'CLARO',
                                                                    'doc_count' => 3353,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_BOLETO',
                                                                                            'doc_count' => 1137,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_FACIL',
                                                                                            'doc_count' => 1105,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_PRE',
                                                                                            'doc_count' => 1092,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'CLARO_POS',
                                                                                            'doc_count' => 19,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            1 =>
                                                                array(
                                                                    'key' => 'VIVO',
                                                                    'doc_count' => 2329,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE',
                                                                                            'doc_count' => 1098,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'CONTROLE_CARTAO',
                                                                                            'doc_count' => 769,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_PRE',
                                                                                            'doc_count' => 455,
                                                                                        ),
                                                                                    3 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_POS_PAGO',
                                                                                            'doc_count' => 5,
                                                                                        ),
                                                                                    4 =>
                                                                                        array(
                                                                                            'key' => 'VIVO_INTERNET_MOVEL_POS',
                                                                                            'doc_count' => 2,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            2 =>
                                                                array(
                                                                    'key' => 'TIM',
                                                                    'doc_count' => 1475,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'TIM_EXPRESS',
                                                                                            'doc_count' => 1115,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'TIM_CONTROLE_FATURA',
                                                                                            'doc_count' => 357,
                                                                                        ),
                                                                                    2 =>
                                                                                        array(
                                                                                            'key' => 'TIM_PRE_PAGO',
                                                                                            'doc_count' => 3,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                            3 =>
                                                                array(
                                                                    'key' => 'OI',
                                                                    'doc_count' => 1253,
                                                                    'operation_count' =>
                                                                        array(
                                                                            'doc_count_error_upper_bound' => 0,
                                                                            'sum_other_doc_count' => 0,
                                                                            'buckets' =>
                                                                                array(
                                                                                    0 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_CARTAO',
                                                                                            'doc_count' => 1058,
                                                                                        ),
                                                                                    1 =>
                                                                                        array(
                                                                                            'key' => 'OI_CONTROLE_BOLETO',
                                                                                            'doc_count' => 195,
                                                                                        ),
                                                                                ),
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),
                                ),
                        ),
                ),
        );
    }
}

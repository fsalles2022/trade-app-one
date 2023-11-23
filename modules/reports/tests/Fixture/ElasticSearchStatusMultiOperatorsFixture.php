<?php

namespace Reports\Tests\Fixture;

class ElasticSearchStatusMultiOperatorsFixture
{
    public static function getSaleArray()
    {
        return array(
        'took' => 0,
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
          'total' => 102,
          'max_score' => 1,
          'hits' =>
          array(
            0 =>
            array(
              '_index' => 'tao',
              '_type' => 'doc',
              '_id' => '2018073115227210-0',
              '_score' => 1,
              '_source' =>
              array(
                'service_imei' => '000000000000000',
                'service_msisdn' => '+5511994013590',
                'service_checkingaccount' => null,
                'service_invoicetype' => 'VIA_POSTAL',
                'service_operatoridentifiers_idvenda' => null,
                'service_mode' => 'PORTABILITY',
                'pointofsale_id' => null,
                'service_customer_mainphone' => '+5511956226555',
                'service_customer_zipcode' => '06160100',
                'service_servicetransaction' => '2018073115227210-0',
                'user_role' => 'vendedor',
                'service_device_penalty' => null,
                'user_id' => null,
                'service_token' => null,
                'service_customer_firstname' => 'Maria',
                'service_duedate' => 1,
                'service_fromoperator' => null,
                'service_statusthirdparty' => 'ATIVADO',
                'service_customer_profession' => null,
                'service_product' => '40',
                'user_areacode' => '11',
                'pointofsale_network_companyname' => 'TradeUp Group',
                'service_pricerecurrent' => null,
                '@version' => '1',
                'user_firstname' => 'RAFAEL SANDIM',
                'service_operatoridentifiers_venda_id' => 1522732,
                'service_device_device' => null,
                'service_customer_localid' => 380,
                'pointofsale_provideridentifiers_clarobr' => null,
                'service_customer_salaryrange' => null,
                'service_customer_number' => '56345',
                'service_operation' => 'CONTROLE_BOLETO',
                'service_operator' => 'CLARO',
                'service_customer_state' => 'SP',
                'service_portednumber' => '11983512532',
                'pointofsale_city' => 'Barueri',
                'pointofsale_cnpj' => '22696923000162',
                'service_valueadhesion' => null,
                'service_price' => '79.99',
                'service_device_pricewith' => null,
                'pointofsale_network_label' => 'TradeUp Group',
                'user_cpf' => '01296802140',
                'service_bankid' => null,
                'service_customer_cpf' => '00845613278',
                'service_label' => 'Controle Mais 5GB + Minutos Ilimitados',
                'created_at' => '2018-07-31T18:22:20.000Z',
                'service_customer_city' => 'Osasco',
                'service_from' => null,
                'pointofsale_network_slug' => 'tradeup-group',
                'pointofsale_provideridentifiers_oi' => null,
                'service_customer_neighborhood' => 'Bandeiras',
                'service_customer_gender' => 'F',
                '@timestamp' => '2018-08-02T21:03:20.598Z',
                'pointofsale_state' => 'SP',
                'service_operatoridentifiers_servico_id' => 1478619,
                'service_customer_rglocal' => 'sspsp',
                'pointofsale_slug' => 'matriz-rio-negro',
                'service_sector' => 'TELECOMMUNICATION',
                'user_lastname' => 'matriz-rio-negro',
                'service_customer_secondaryphone' => '+5511956235586',
                'service_customer_maritalstatus' => null,
                'service_cvv' => null,
                'updated_at' => '2018-07-31T18:22:20.000Z',
                'pointofsale_label' => 'Matriz - Rio Negro',
                'service_promotionlabel' => 'CONTROLE MAIS Com Whatsapp - 75496',
                'service_areacode' => null,
                'pointofsale_areacode' => '11',
                'service_promotion' => 71,
                'user_email' => 'rafael.sandim@tradeupgroup.com',
                'service_operatoridentifiers_idservico' => null,
                'service_agency' => null,
                'pointofsale_network_cnpj' => '22696923000162',
                'pointofsale_network_id' => 1,
                'service_customer_local' => 'Rua Gen Hasegawa',
                'service_customer_rgdate' => '2003-03-28',
                'service_customer_filiation' => 'Joana Jesus',
                'pointofsale_tradingname' => 'Trade Up Serviço de Apoio Administrativo e Comércio de Equipamentos de Telefonia e Comunicação LTDA',
                'service_customer_rg' => '001494951',
                'service_device_pricewithout' => null,
                'service_status' => 'ACCEPTED',
                'pointofsale_companyname' => 'TradeUp Group',
                'service_customer_lastname' => 'Jesus',
                'service_iccid' => '89550536110018673085',
                'mongo_id' => '5B63717CDF03259611B6A905',
              ),
            ),
          ),
        ),
        'aggregations' =>
        array(
          'status_count' =>
          array(
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'buckets' =>
            array(
              0 =>
              array(
                'key' => 'ACCEPTED',
                'doc_count' => 69,
              ),
              1 =>
              array(
                'key' => 'APPROVED',
                'doc_count' => 33,
              ),
            ),
          ),
        ),
        );
    }
}

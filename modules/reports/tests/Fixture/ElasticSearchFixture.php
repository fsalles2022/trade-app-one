<?php

namespace Reports\Tests\Fixture;

class ElasticSearchFixture
{
    public static function getSaleArray()
    {
        return [
            'took'      => 5,
            'timed_out' => false,
            '_shards'   =>
                [
                    'total'      => 5,
                    'successful' => 5,
                    'skipped'    => 0,
                    'failed'     => 0,
                ],
            'hits'      =>
                [
                    'total'     => 1,
                    'max_score' => 1,
                    'hits'      =>
                        [
                            0 =>
                                [
                                    '_index'  => 'tao',
                                    '_type'   => 'vendas',
                                    '_id'     => '101164.94665',
                                    '_score'  => 1,
                                    '_source' =>
                                        [
                                            'cliente_data_nascimento'         => null,
                                            'rede_nome'                       => 'LASER',
                                            'cliente_logradouro_tipo_id'      => null,
                                            'usuario_cidade'                  => null,
                                            'funcao_nome'                     => 'PROMOTOR',
                                            'usuario_funcao_id'               => 9,
                                            'gerente_sellin_nome'             => null,
                                            'cliente_cidade_ddd'              => '11',
                                            'pdv_nome'                        => 'LASER ELETRO',
                                            'servico_numero_acesso'           => '+5581994721874',
                                            'usuario_cpf'                     => '06870676429',
                                            'usuario_nome_rede_regional'      => 'LEANDRO GOMES DE BARROS - LASER - NE',
                                            'venda_total'                     => 51.950000000000003,
                                            'cliente_nome'                    => 'Kacia Regina Reis Côrtes',
                                            'servico_tipo_servico'            => 'MIGRACAO',
                                            'bko_cpf'                         => null,
                                            'servico_valor'                   => 51.950000000000003,
                                            'venda_updated_at'                => '2017-08-27T16:18:32.000Z',
                                            'usuario_ativo'                   => true,
                                            'cliente_cpf'                     => '45423903572',
                                            'pdv_codigo'                      => 'BJU2',
                                            'usuario_cep'                     => null,
                                            'servico_updated_at'              => '2017-08-27T16:19:35.000Z',
                                            'cliente_rg'                      => null,
                                            'cliente_estado_civil_id'         => null,
                                            'servico_plano_tipo'              => 'CONTROLE_FACIL',
                                            'servico_id'                      => 94665,
                                            'pdv_ddd'                         => 81,
                                            'pdv_id'                          => 6480,
                                            'tags'                            =>
                                                [
                                                    0 => 'vendas',
                                                ],
                                            'servico_aceite_voz'              => null,
                                            'bko_nome'                        => null,
                                            'usuario_id'                      => 22473,
                                            'pdv_nome_regional_e_codigo'      => 'LASER ELETRO - BJU2',
                                            'venda_client'                    => 'WEB',
                                            'venda_created_at'                => '2017-08-27T16:18:32.000Z',
                                            'servico_nome_aparelho'           => null,
                                            'pdv_uf'                          => 'PE',
                                            'servico_created_at'              => '2017-08-27T16:18:32.000Z',
                                            'usuario_uf'                      => null,
                                            'cliente_faixa_salarial_id'       => null,
                                            'servico_status'                  => 'APROVADO',
                                            'cliente_id'                      => 63951,
                                            'servico_portabilidade'           => null,
                                            'venda_pdv_id'                    => 6480,
                                            'servico_ddd'                     => 81,
                                            'promotor_carteira_username'      => null,
                                            'venda_updated_by'                => null,
                                            'servico_imei'                    => 'Não Informado',
                                            'regional_pdv'                    => 'NE',
                                            'cliente_uf'                      => null,
                                            'servico_tipo_fatura'             => 'CARTAO_CREDITO',
                                            'canal_id'                        => 1,
                                            'usuario_ultimo_acesso'           => '2017-08-31T00:00:00.000Z',
                                            'canal_nome'                      => 'CLARO_VAREJO',
                                            'rede_label'                      => 'LASER',
                                            'funcao_label'                    => 'Promotor',
                                            'rede_id'                         => 240,
                                            'servico_preco_pre'               => 0,
                                            '@version'                        => '1',
                                            'operadora_nome'                  => 'CLARO',
                                            'venda_id'                        => 101164,
                                            'servico_preco_aparelho_plano'    => 0,
                                            'pdv_codigo_nome_rede_e_regional' => 'BJU2 - LASER - NE',
                                            'pdv_rede_id'                     => 240,
                                            'gerente_sellin_cpf'              => null,
                                            'usuario_data_nascimento'         => null,
                                            'usuario_primeiro_nome'           => 'LEANDRO GOMES DE BARROS',
                                            'promotor_carteira_cpf'           => null,
                                            'cliente_cidade'                  => null,
                                            'pdv_habitado'                    => 1,
                                            'canal_label'                     => 'Varejo',
                                            'regional_ddd'                    => 'NE',
                                            'servico_plano'                   => 'CLARO_CONTROLE_R$51,95_+_3GB_+_ILIMITADO',
                                            'usuario_nome'                    => 'LEANDRO GOMES DE BARROS',
                                            '@timestamp'                      => '2018-04-03T05:37:29.238Z',
                                            'partner'                         => 'Siv',
                                            'servico_operadora'               => 'Claro',
                                            'cliente_genero'                  => 'M',
                                            'cliente_bairro'                  => null,
                                            'pdv_cnpj'                        => null,
                                            'pdv_cidade'                      => 'JABOATAO DOS GUARARAPES',
                                            'regional_id'                     => 7,
                                            'pdv_ativo'                       => true,
                                        ],
                                ]
                        ],
                ]
        ];
    }
}

<?php

namespace VivoBR\Tests\Unit\Services;

use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Tests\TestCase;
use VivoBR\Services\MapSunSalesService;
use VivoBR\Tests\Fixtures\SalesFromSun;

class MapSunSalesServiceTest extends TestCase
{
    /** @test */
    public function running_map()
    {
        $data = [
            [
                "id"               => "SP-414903",
                "canal"            => "VAREJO",
                "uf"               => "SP",
                "status"           => "FINALIZADA",
                "analiseExecutada" => 1,
                "cnpjPdv"          => "45242914002140",
                "pdvNomeFantasia"  => "C&A MODAS",
                "adabas"           => "11476",
                "rede"             => "C&A",
                "redeNomeFantasia" => "C&A",
                "cpfVendedor"      => "38534143897",
                "data"             => "2018-07-05 16:12:37",
                "dataAlteracao"    => "2018-07-05 16:15:53",
                "latitude"         => 0,
                "longitude"        => 0,
                "nomeCadastro"     => "IASCARA RODRIGUES OLIVEIRA LIMA",
                "cpfCadastro"      => "38534143897",
                "servicos"         => [
                    [
                        "id"                  => 424812,
                        "status"              => "REPROVADO_INDISPONIBILIDADE",
                        "idPlano"             => 1432,
                        "nomePlano"           => "VIVO CONTROLE DIGITAL - 2GB",
                        "produto"             => "CONTROLE",
                        "ddd"                 => 11,
                        "tipoServico"         => "MIGRACAO",
                        "portabilidade"       => false,
                        "fidelizacao"         => false,
                        "iccid"               => null,
                        "imei"                => null,
                        "numeroAcesso"        => 11982107549,
                        "numeroPortabilidade" => 0,
                        "operadora"           => 0,
                        "tipoFatura"          => "E-mail",
                        "vencimento"          => 1,
                        "d0"                  => 0,
                        "url_m4u"             => null,
                        "url_m4uplana"        => null,
                        "numeroProvisorio"    => null
                    ]
                ],
                "pessoa"           => [
                    "cpf"            => "38534143897",
                    "nome"           => "IASCARA RODRIGUES OLIVEIRA LIMA",
                    "sexo"           => "F",
                    "filiacao"       => "MARIA GORETE RODRIGUES DA SILVA",
                    "dataNascimento" => "1989-04-27",
                    "telefone1"      => 5511982107549,
                    "telefone2"      => 551158956060,
                    "email"          => "iascaralima@yahoo.com.br",
                    "cep"            => "04950060",
                    "logradouro"     => "RUA UTUCURA",
                    "complemento"    => "",
                    "bairro"         => "CIDADE IPAVA",
                    "numero"         => 17,
                    "semNumero"      => 0,
                    "cidade"         => "São Paulo",
                    "UF"             => "SP"
                ],
                "origem"           => "APK",
                "observacoes"      => [],
                "logObservacoes"   => [],
                "ativo"            => true
            ]
        ];


        $importation = resolve(MapSunSalesService::class);
        $result      = $importation->mapToTable('sun', collect($data));

        self::assertEquals(Modes::MIGRATION, data_get($result->first(), 'service_mode'));
    }

    /** @test */
    public function running_map_portability()
    {
        $data = [
            [
                "id"               => "SP-414903",
                "canal"            => "VAREJO",
                "uf"               => "SP",
                "status"           => "FINALIZADA",
                "analiseExecutada" => 1,
                "cnpjPdv"          => "45242914002140",
                "pdvNomeFantasia"  => "C&A MODAS",
                "adabas"           => "11476",
                "rede"             => "C&A",
                "redeNomeFantasia" => "C&A",
                "cpfVendedor"      => "38534143897",
                "data"             => "2018-07-05 16:12:37",
                "dataAlteracao"    => "2018-07-05 16:15:53",
                "latitude"         => 0,
                "longitude"        => 0,
                "nomeCadastro"     => "IASCARA RODRIGUES OLIVEIRA LIMA",
                "cpfCadastro"      => "38534143897",
                "servicos"         => [
                    [
                        "id"                  => 424812,
                        "status"              => "REPROVADO_INDISPONIBILIDADE",
                        "idPlano"             => 1432,
                        "nomePlano"           => "VIVO CONTROLE DIGITAL - 2GB",
                        "produto"             => "CONTROLE",
                        "ddd"                 => 11,
                        "tipoServico"         => "ALTA",
                        "portabilidade"       => false,
                        "fidelizacao"         => false,
                        "iccid"               => null,
                        "imei"                => null,
                        "numeroAcesso"        => 0,
                        "numeroPortabilidade" => 11982107549,
                        "operadora"           => 0,
                        "tipoFatura"          => "E-mail",
                        "vencimento"          => 1,
                        "d0"                  => 0,
                        "url_m4u"             => null,
                        "url_m4uplana"        => null,
                        "numeroProvisorio"    => null
                    ]
                ],
                "pessoa"           => [
                    "cpf"            => "38534143897",
                    "nome"           => "IASCARA RODRIGUES OLIVEIRA LIMA",
                    "sexo"           => "F",
                    "filiacao"       => "MARIA GORETE RODRIGUES DA SILVA",
                    "dataNascimento" => "1989-04-27",
                    "telefone1"      => 5511982107549,
                    "telefone2"      => 551158956060,
                    "email"          => "iascaralima@yahoo.com.br",
                    "cep"            => "04950060",
                    "logradouro"     => "RUA UTUCURA",
                    "complemento"    => "",
                    "bairro"         => "CIDADE IPAVA",
                    "numero"         => 17,
                    "semNumero"      => 0,
                    "cidade"         => "São Paulo",
                    "UF"             => "SP"
                ],
                "origem"           => "WEB",
                "observacoes"      => [],
                "logObservacoes"   => [],
                "ativo"            => true
            ]
        ];


        $importation = resolve(MapSunSalesService::class);
        $result      = $importation->mapToTable('sun', collect($data));

        self::assertEquals(Modes::PORTABILITY, data_get($result->first(), 'service_mode'));
    }

    /** @test */
    public function running_map_status_not_found()
    {
        $data = [
            [
                "id"               => "SP-414903",
                "canal"            => "VAREJO",
                "uf"               => "SP",
                "status"           => "FINALIZADA",
                "analiseExecutada" => 1,
                "cnpjPdv"          => "45242914002140",
                "pdvNomeFantasia"  => "C&A MODAS",
                "adabas"           => "11476",
                "rede"             => "C&A",
                "redeNomeFantasia" => "C&A",
                "cpfVendedor"      => "38534143897",
                "data"             => "2018-07-05 16:12:37",
                "dataAlteracao"    => "2018-07-05 16:15:53",
                "latitude"         => 0,
                "longitude"        => 0,
                "nomeCadastro"     => "IASCARA RODRIGUES OLIVEIRA LIMA",
                "cpfCadastro"      => "38534143897",
                "servicos"         => [
                    [
                        "id"                  => 424812,
                        "status"              => "Nao existente",
                        "idPlano"             => 1432,
                        "nomePlano"           => "VIVO CONTROLE DIGITAL - 2GB",
                        "produto"             => "CONTROLE",
                        "ddd"                 => 11,
                        "tipoServico"         => "ALTA",
                        "portabilidade"       => false,
                        "fidelizacao"         => false,
                        "iccid"               => null,
                        "imei"                => null,
                        "numeroAcesso"        => 0,
                        "numeroPortabilidade" => 11982107549,
                        "operadora"           => 0,
                        "tipoFatura"          => "E-mail",
                        "vencimento"          => 1,
                        "d0"                  => 0,
                        "url_m4u"             => null,
                        "url_m4uplana"        => null,
                        "numeroProvisorio"    => null
                    ]
                ],
                "pessoa"           => [
                    "cpf"            => "38534143897",
                    "nome"           => "IASCARA RODRIGUES OLIVEIRA LIMA",
                    "sexo"           => "F",
                    "filiacao"       => "MARIA GORETE RODRIGUES DA SILVA",
                    "dataNascimento" => "1989-04-27",
                    "telefone1"      => 5511982107549,
                    "telefone2"      => 551158956060,
                    "email"          => "iascaralima@yahoo.com.br",
                    "cep"            => "04950060",
                    "logradouro"     => "RUA UTUCURA",
                    "complemento"    => "",
                    "bairro"         => "CIDADE IPAVA",
                    "numero"         => 17,
                    "semNumero"      => 0,
                    "cidade"         => "São Paulo",
                    "UF"             => "SP"
                ],
                "origem"           => "WEB",
                "observacoes"      => [],
                "logObservacoes"   => [],
                "ativo"            => true
            ]
        ];


        $importation = resolve(MapSunSalesService::class);
        $result      = $importation->mapToTable('sun', collect($data));

        self::assertEquals('-', data_get($result->first(), 'service_status'));
    }

    /** @test */
    public function running_map_status_empty_when_origem_is_api()
    {
        $data = [
            [
                "id"               => "SP-414903",
                "canal"            => "VAREJO",
                "uf"               => "SP",
                "status"           => "FINALIZADA",
                "analiseExecutada" => 1,
                "cnpjPdv"          => "45242914002140",
                "pdvNomeFantasia"  => "C&A MODAS",
                "adabas"           => "11476",
                "rede"             => "C&A",
                "redeNomeFantasia" => "C&A",
                "cpfVendedor"      => "38534143897",
                "data"             => "2018-07-05 16:12:37",
                "dataAlteracao"    => "2018-07-05 16:15:53",
                "latitude"         => 0,
                "longitude"        => 0,
                "nomeCadastro"     => "IASCARA RODRIGUES OLIVEIRA LIMA",
                "cpfCadastro"      => "38534143897",
                "servicos"         => [
                    [
                        "id"                  => 424812,
                        "status"              => "Nao existente",
                        "idPlano"             => 1432,
                        "nomePlano"           => "VIVO CONTROLE DIGITAL - 2GB",
                        "produto"             => "CONTROLE",
                        "ddd"                 => 11,
                        "tipoServico"         => "ALTA",
                        "portabilidade"       => false,
                        "fidelizacao"         => false,
                        "iccid"               => null,
                        "imei"                => null,
                        "numeroAcesso"        => 0,
                        "numeroPortabilidade" => 11982107549,
                        "operadora"           => 0,
                        "tipoFatura"          => "E-mail",
                        "vencimento"          => 1,
                        "d0"                  => 0,
                        "url_m4u"             => null,
                        "url_m4uplana"        => null,
                        "numeroProvisorio"    => null
                    ]
                ],
                "pessoa"           => [
                    "cpf"            => "38534143897",
                    "nome"           => "IASCARA RODRIGUES OLIVEIRA LIMA",
                    "sexo"           => "F",
                    "filiacao"       => "MARIA GORETE RODRIGUES DA SILVA",
                    "dataNascimento" => "1989-04-27",
                    "telefone1"      => 5511982107549,
                    "telefone2"      => 551158956060,
                    "email"          => "iascaralima@yahoo.com.br",
                    "cep"            => "04950060",
                    "logradouro"     => "RUA UTUCURA",
                    "complemento"    => "",
                    "bairro"         => "CIDADE IPAVA",
                    "numero"         => 17,
                    "semNumero"      => 0,
                    "cidade"         => "São Paulo",
                    "UF"             => "SP"
                ],
                "origem"           => "API",
                "observacoes"      => [],
                "logObservacoes"   => [],
                "ativo"            => true
            ]
        ];


        $importation = resolve(MapSunSalesService::class);
        $result      = $importation->mapToTable('sun', collect($data));

        self::assertEquals(0, $result->count());
    }

    /** @test */
    public function running_two_rows_and_return_one_when_origem_is_api()
    {
        $data = [
            [
                "id"               => "SP-414903",
                "canal"            => "VAREJO",
                "uf"               => "SP",
                "status"           => "FINALIZADA",
                "analiseExecutada" => 1,
                "cnpjPdv"          => "45242914002140",
                "pdvNomeFantasia"  => "C&A MODAS",
                "servicos"         => [
                    [
                        "id" => 424812,
                    ]
                ],
                "pessoa"           => [
                    "cpf" => "38534143897",
                ],
                "origem"           => "API",
                "observacoes"      => [],
                "logObservacoes"   => [],
                "ativo"            => true
            ],
            [
                "id"             => "SP-414903",
                "cnpjPdv"        => "45242914002140",
                "servicos"       => [
                    [
                        "id" => 424812,
                    ]
                ],
                "pessoa"         => [
                    "cpf" => "38534143897"
                ],
                "origem"         => "WEB"
            ]
        ];


        $importation = resolve(MapSunSalesService::class);
        $result      = $importation->mapToTable('sun', collect($data));

        self::assertEquals(1, $result->count());
    }

    /** @test */
    public function should_return_sales_with_origin_api()
    {
        $list          = SalesFromSun::allSalesFromNetworks();
        $sales         = collect($list['vendas']);
        $serviceMapper = new MapSunSalesService();
        $return        = $serviceMapper->mapToTable('', $sales);
        self::assertEquals(315, $return->count());
    }
}

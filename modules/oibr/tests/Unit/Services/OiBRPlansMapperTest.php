<?php

namespace OiBR\Tests\Unit\Services;

use OiBR\Services\OiBRMapPlansService;
use TradeAppOne\Tests\TestCase;

class OiBRPlansMapperTest extends TestCase
{
    /** @test */
    public function should_map_and_fliter_blacklist_and_return_whitelist()
    {
        $mapped = OiBRMapPlansService::map($this->responseOfOi());
        self::assertNotEmpty($mapped->where('product', 'OCSF110'));
        self::assertEmpty($mapped->whereIn('product', OiBRMapPlansService::BLACKLIST_PLANS));
    }

    protected function responseOfOi()
    {
        return [
            [
                "id"               => "5d9c1e1b-4beb-44bf-82f4-1ec2c7efafc4",
                "nome"             => "OCSF110",
                "nomeComercial"    => "A - Oi Mais Controle Intermediário G2 R$39,99",
                "descricao"        => "Oi Mais Controle: Fale ilimitado com qualquer operadora de todo o Brasil, 4GB de Internet 4G, Whatsapp, Messenger e SMS. Tudo isso por RS39,99/mês.",
                "valorRecorrencia" => 3981,
                "valorAdesao"      => 3981,
                "retencao"         => false
            ],
            [
                "id"               => "1fda23ee-86c3-4161-8583-8447ed7e1327",
                "nome"             => "OCSF126",
                "nomeComercial"    => "A - Oi Mais Controle Avançado BS - R$54,99",
                "descricao"        => "Oi Mais Controle: Fale ilimitado com qualquer operadora de todo o Brasil, 6GB de Internet 4G, Whatsapp, Messenger, Facebook e SMS. Tudo isso por RS54,99/mês",
                "valorRecorrencia" => 5485,
                "valorAdesao"      => 5485,
                "retencao"         => false
            ],
            [
                "id"               => "a3d08901-31c5-44ae-9212-5e162c525610",
                "nome"             => "OCSF158",
                "nomeComercial"    => "A - Oi Mais Controle Smart",
                "descricao"        => "Tenha 15GB de internet 4G, WhatsApp, Messenger, Facebook, Instagram, minutos ilimitados e SMSs para qualquer operadora do Brasil. Tudo isso por apenas R$99,90 por mês.",
                "valorRecorrencia" => 9989,
                "valorAdesao"      => 9989,
                "retencao"         => false
            ]
        ];
    }
}

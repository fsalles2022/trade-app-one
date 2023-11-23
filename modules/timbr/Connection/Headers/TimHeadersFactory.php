<?php

namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class TimHeadersFactory
{
    private static $CONNECTIONS = [
        NetworkEnum::CEA            => CeATim::class,
        NetworkEnum::RIACHUELO      => RiachueloTim::class,
        NetworkEnum::PERNAMBUCANAS  => PernambucanasTim::class,
        NetworkEnum::TAQI           => TaQiTim::class,
        NetworkEnum::LEBES          => LebesTim::class,
        NetworkEnum::EXTRA          => ExtraTim::class,
        NetworkEnum::SCHUMANN       => Schumann::class,
        NetworkEnum::FUJIOKA        => FujiokaTim::class,
        NetworkEnum::ELETROZEMA     => EletrozemaTim::class,
        NetworkEnum::VERTEX         => VertexTim::class,
        NetworkEnum::CASAEVIDEO     => CasaEVideoTim::class,
        NetworkEnum::IBYTE          => IbyteTim::class,
        NetworkEnum::VIA_VAREJO     => ViaVarejoTim::class,
        NetworkEnum::AVENIDA        => AvenidaTim::class,
        NetworkEnum::COLOMBO        => ColomboTim::class,
        NetworkEnum::MULTISOM       => MultisomTim::class,
        NetworkEnum::LOJAS_TORRA    => TorraTim::class,
        NetworkEnum::LE_BISCUIT     => LeBiscuit::class,
        NetworkEnum::MERCADO_MOVEIS => MercadoMoveisTim::class,
        NetworkEnum::NOVO_MUNDO     => NovoMundoTim::class,
        NetworkEnum::MAGAZAN        => MagazanTim::class,
        NetworkEnum::SAMSUNG        => SamsungTim::class,
        NetworkEnum::SAMSUNG_MRF    => SamsungMRFTim::class,
        NetworkEnum::MASTERCELL     => MastercellTim::class,
        NetworkEnum::IPLACE         => IplaceTim::class,
        'rede'                      => CeATim::class //Network used for tests
    ];

    public static function make(string $strategy): TimHeader
    {
        return resolve(self::$CONNECTIONS[$strategy]);
    }
}

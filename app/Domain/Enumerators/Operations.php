<?php

namespace TradeAppOne\Domain\Enumerators;

use Buyback\Models\Operations\Brused;
use Buyback\Models\Operations\Iplace;
use Buyback\Models\Operations\Ipad;
use Buyback\Models\Operations\SaldaoInformatica;
use Buyback\Models\Operations\TradeNet;
use Buyback\Models\Operations\TradeUp;
use Buyback\Models\Operations\Watch;
use ClaroBR\Models\ClaroBandaLarga;
use ClaroBR\Models\ClaroPos;
use ClaroBR\Models\ClaroPre;
use ClaroBR\Models\ControleBoleto;
use ClaroBR\Models\ControleFacil;
use Generali\Models\Generali;
use Mapfre\Models\RouboFurto;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Models\McAfeeMultiAccess;
use Movile\Models\MovileCubes;
use NextelBR\Models\NextelBRControleBoleto;
use NextelBR\Models\NextelBRControleCartao;
use OiBR\Models\OiBRControleBoleto;
use OiBR\Models\OiBRControleCartao;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use SurfPernambucanas\Models\SurfPernambucanasSmartControl;
use TimBR\Models\TimBRBlack;
use TimBR\Models\TimBRBlackExpress;
use TimBR\Models\TimBRBlackMulti;
use TimBR\Models\TimBRBlackMultiDependent;
use TimBR\Models\TimBRControleFatura;
use TimBR\Models\TimBRControleFlex;
use TimBR\Models\TimBRExpress;
use TimBR\Models\TimBRPrePago;
use TradeAppOne\Domain\Enumerators\Operations\UolOperations;
use VivoBR\Models\VivoBRPrePago;
use VivoBR\Models\VivoControle;
use VivoBR\Models\VivoControleCartao;
use VivoBR\Models\VivoInternetMovelPos;
use VivoBR\Models\VivoPosPago;

final class Operations
{
    public const TELECOMMUNICATION = 'TELECOMMUNICATION'; //Deprecated
    public const LINE_ACTIVATION   = 'LINE_ACTIVATION';

    public const CLARO                   = 'CLARO';
    public const CLARO_CONTROLE_BOLETO   = 'CONTROLE_BOLETO';
    public const CLARO_CONTROLE_FACIL    = 'CONTROLE_FACIL';
    public const CLARO_PRE               = 'CLARO_PRE';
    public const CLARO_PRE_EXTERNAL_SALE = 'CLARO_PRE_EXTERNAL_SALE';
    public const CLARO_POS               = 'CLARO_POS';
    public const CLARO_BANDA_LARGA       = 'CLARO_BANDA_LARGA';
    public const BANDA_LARGA             = 'BANDA_LARGA';
    public const CLARO_VOZ_DADOS         = 'CLARO_VOZ_DADOS';
    public const CLARO_CONTROLE          = 'CLARO_CONTROLE';
    public const CLARO_DADOS             = 'CLARO_DADOS';
    public const CLARO_RESIDENCIAL       = 'CLARO_RESIDENCIAL';
    public const CLARO_PONTO_ADICIONAL   = 'PONTO_ADICIONAL';
    public const RESIDENCIAL             = 'RESIDENCIAL';
    public const CLARO_TELEVISAO         = 'TELEVISAO';
    public const CLARO_FIXO              = 'FIXO';
    public const CLARO_TV_PRE            = 'CLARO_TV_PRE';
    public const VERTEX                  = 'VERTEX';

    public const CLARO_RESIDENTIAL = [
        self::CLARO_TELEVISAO,
        self::CLARO_BANDA_LARGA,
        self::CLARO_FIXO,
        self::CLARO_PONTO_ADICIONAL,
    ];

    public const CLARO_RESIDENTIAL_STATUS_IMPORT = [
        self::CLARO_TELEVISAO,
        self::BANDA_LARGA,
        self::CLARO_FIXO,
        self::CLARO_PONTO_ADICIONAL,
    ];

    public const VIVO                    = 'VIVO';
    public const VIVO_POS_PAGO           = 'VIVO_POS_PAGO';
    public const VIVO_CONTROLE           = 'CONTROLE';
    public const VIVO_CONTROLE_CARTAO    = 'CONTROLE_CARTAO';
    public const VIVO_PRE                = 'VIVO_PRE';
    public const VIVO_INTERNET_MOVEL_POS = 'VIVO_INTERNET_MOVEL_POS';

    public const TIM                        = 'TIM';
    public const TIM_EXPRESS                = 'TIM_EXPRESS';
    public const TIM_CONTROLE_FATURA        = 'TIM_CONTROLE_FATURA';
    public const TIM_PRE_PAGO               = 'TIM_PRE_PAGO';
    public const TIM_POS_PAGO               = 'TIM_POS_PAGO';
    public const TIM_CONTROLE_FLEX          = 'TIM_CONTROLE_FLEX';
    public const TIM_BLACK                  = 'TIM_BLACK';
    public const TIM_BLACK_EXPRESS          = 'TIM_BLACK_EXPRESS';
    public const TIM_BLACK_MULTI            = 'TIM_BLACK_MULTI';
    public const TIM_BLACK_MULTI_DEPENDENT  = 'TIM_BLACK_MULTI_DEPENDENT';

    public const TIM_PREMIUM_RETAIL_OPERATIONS = [
        self::TIM_BLACK,
        self::TIM_BLACK_MULTI,
        self::TIM_BLACK_EXPRESS,
        self::TIM_CONTROLE_FATURA,
        self::TIM_BLACK_MULTI_DEPENDENT,
    ];

    public const OI                 = 'OI';
    public const OI_CONTROLE_CARTAO = 'OI_CONTROLE_CARTAO';
    public const OI_CONTROLE_BOLETO = 'OI_CONTROLE_BOLETO';
    public const OI_RESIDENCIAL     = 'OI_RESIDENCIAL';

    public const NEXTEL                 = 'NEXTEL';
    public const NEXTEL_CONTROLE_CARTAO = 'NEXTEL_CONTROLE_CARTAO';
    public const NEXTEL_CONTROLE_BOLETO = 'NEXTEL_CONTROLE_BOLETO';

    public const SURF_PERNAMBUCANAS               = 'SURF_PERNAMBUCANAS';
    public const SURF_PERNAMBUCANAS_PRE           = 'SURF_PERNAMBUCANAS_PRE';
    public const SURF_PERNAMBUCANAS_PRE_RECHARGE  = 'SURF_PERNAMBUCANAS_PRE_RECHARGE';
    public const SURF_PERNAMBUCANAS_SMART_CONTROL = 'SURF_PERNAMBUCANAS_SMART_CONTROL';

    public const SURF_CORREIOS               = 'SURF_CORREIOS';
    public const SURF_CORREIOS_PRE           = 'SURF_CORREIOS_PRE';
    public const SURF_CORREIOS_PRE_RECHARGE  = 'SURF_CORREIOS_PRE_RECHARGE';
    public const SURF_CORREIOS_SMART_CONTROL = 'SURF_CORREIOS_SMART_CONTROL';

    public const NET  = 'NET';
    public const CTBC = 'CTBC';

    public const MOBILE_APPS = 'MOBILE_APPS';

    public const MOVILE       = 'MOVILE';
    public const MOVILE_CUBES = 'MOVILE_CUBES';

    public const TRADE_IN = 'TRADE_IN';

    public const TRADE_IN_MOBILE    = 'TRADE_IN_MOBILE';
    public const SALDAO_INFORMATICA = 'SALDAO_INFORMATICA';
    public const BRUSED             = 'BRUSED';
    public const IPLACE             = 'IPLACE';
    public const IPLACE_ANDROID     = 'IPLACE_ANDROID';
    public const IPLACE_IPAD        = 'IPLACE_IPAD';
    public const TRADE_NET          = 'TRADE_NET';
    public const TRADE_UP           = 'TRADE_UP';
    public const WATCH              = 'WATCH';

    public const SECURITY = 'SECURITY_SYSTEM';

    public const MCAFEE                    = 'MCAFEE';
    public const MCAFEE_MULTI_ACCESS       = 'MCAFEE_MULTI_ACCESS';
    public const MCAFEE_MULTI_ACCESS_TRIAL = 'MCAFEE_MULTI_ACCESS_TRIAL';
    public const MCAFEE_MOBILE_SECURITY    = 'MOBILE_SECURITY';

    public const GENERALI             = 'GENERALI';
    public const GENERALI_ELECTRONICS = 'GENERALI_ELECTRONICS';

    public const INSURERS = 'INSURERS';

    public const MAPFRE             = 'MAPFRE';
    public const MAPFRE_ROUBO_FURTO = 'ROUBO_FURTO';

    public const COURSES = 'COURSES';
    public const UOL     = 'UOL';

    public const INSURERS_OPERATORS = [
        self::MAPFRE => [
            self::MAPFRE_ROUBO_FURTO => RouboFurto::class
        ],
        self::GENERALI => [
            self::GENERALI_ELECTRONICS => Generali::class
        ]
    ];

    public const SECURITY_OPERATORS = [
        self::MCAFEE => [
            self::MCAFEE_MOBILE_SECURITY    => McAfeeMobileSecurity::class,
            self::MCAFEE_MULTI_ACCESS       => McAfeeMultiAccess::class,
            self::MCAFEE_MULTI_ACCESS_TRIAL => McAfeeMultiAccess::class
        ]
    ];

    public const TRADE_IN_OPERATORS = [
        self::TRADE_IN_MOBILE => [
            self::SALDAO_INFORMATICA => SaldaoInformatica::class,
            self::BRUSED             => Brused::class,
            self::IPLACE             => Iplace::class,
            self::TRADE_NET          => TradeNet::class,
            self::IPLACE_ANDROID     => Iplace::class,
            self::IPLACE_IPAD        => Ipad::class,
            self::TRADE_UP           => TradeUp::class,
            self::WATCH              => Watch::class
        ]
    ];

    public const TELECOMMUNICATION_OPERATORS = [
        self::CLARO => [
            self::CLARO_CONTROLE_BOLETO => ControleBoleto::class,
            self::CLARO_CONTROLE_FACIL  => ControleFacil::class,
            self::CLARO_PRE             => ClaroPre::class,
            self::CLARO_POS             => ClaroPos::class,
            self::CLARO_BANDA_LARGA     => ClaroBandaLarga::class,
        ],
        self::VIVO => [
            self::VIVO_CONTROLE           => VivoControle::class,
            self::VIVO_CONTROLE_CARTAO    => VivoControleCartao::class,
            self::VIVO_POS_PAGO           => VivoPosPago::class,
            self::VIVO_PRE                => VivoBRPrePago::class,
            self::VIVO_INTERNET_MOVEL_POS => VivoInternetMovelPos::class
        ],
        self::TIM => [
            self::TIM_CONTROLE_FATURA       => TimBRControleFatura::class,
            self::TIM_EXPRESS               => TimBRExpress::class,
            self::TIM_PRE_PAGO              => TimBRPrePago::class,
            self::TIM_CONTROLE_FLEX         => TimBRControleFlex::class,
            self::TIM_BLACK                 => TimBRBlack::class,
            self::TIM_BLACK_MULTI           => TimBRBlackMulti::class,
            self::TIM_BLACK_MULTI_DEPENDENT => TimBRBlackMultiDependent::class,
            self::TIM_BLACK_EXPRESS         => TimBRBlackExpress::class,
        ],
        self::OI => [
            self::OI_CONTROLE_BOLETO => OiBRControleBoleto::class,
            self::OI_CONTROLE_CARTAO => OiBRControleCartao::class,
        ],
        self::NEXTEL => [
            self::NEXTEL_CONTROLE_CARTAO => NextelBRControleCartao::class,
            self::NEXTEL_CONTROLE_BOLETO => NextelBRControleBoleto::class,
        ],
        self::SURF_PERNAMBUCANAS => [
            self::SURF_PERNAMBUCANAS_PRE => SurfPernambucanasPrePago::class,
            self::SURF_PERNAMBUCANAS_PRE_RECHARGE => SurfPernambucanasPrePago::class,
            self::SURF_PERNAMBUCANAS_SMART_CONTROL => SurfPernambucanasSmartControl::class
        ],
        self::SURF_CORREIOS => [
            self::SURF_CORREIOS_PRE => SurfPernambucanasPrePago::class,
            self::SURF_CORREIOS_PRE_RECHARGE => SurfPernambucanasPrePago::class,
            self::SURF_CORREIOS_SMART_CONTROL => SurfPernambucanasSmartControl::class
        ],
    ];

    public const COURSES_OPERATORS = [
        self::UOL => UolOperations::OPERATORS
    ];

    public const SECTORS = [
        self::TELECOMMUNICATION => self::TELECOMMUNICATION_OPERATORS,
        self::INSURERS          => self::INSURERS_OPERATORS,
        self::SECURITY          => self::SECURITY_OPERATORS,
        self::MOBILE_APPS       => self::MOBILE_APPS_OPERATORS,
        self::TRADE_IN          => self::TRADE_IN_OPERATORS,
        self::COURSES           => self::COURSES_OPERATORS
    ];

    public const MOBILE_APPS_OPERATORS = [
        self::MOVILE => [
            self::MOVILE_CUBES => MovileCubes::class
        ]
    ];
}

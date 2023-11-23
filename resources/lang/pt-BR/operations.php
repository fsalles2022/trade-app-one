<?php

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Operations\UolOperations;

return [
    Operations::CLARO                   => 'Claro',
    Operations::VIVO                    => 'Vivo',
    Operations::TIM                     => 'TIM',
    Operations::OI                      => 'Oi',
    Operations::UOL                     => 'Uol',
    Operations::NEXTEL                  => 'Nextel',
    Operations::TELECOMMUNICATION       => 'Ativação de linha',
    Operations::SECURITY                => 'Aplicativos de segurança',
    Operations::MOBILE_APPS             => 'Aplicativos',
    Operations::COURSES                 => 'Cursos',
    Operations::TRADE_IN                => 'TradeIn',
    Operations::TRADE_IN_MOBILE         => 'TradeIn Mobile',
    Operations::MOVILE                  => 'Disney Cubes',
    Operations::MCAFEE                  => 'Mcafee',
    Operations::LINE_ACTIVATION         => 'Linhas de Operadoras',
    Operations::INSURERS                => 'Seguros',
    Operations::SURF_PERNAMBUCANAS      => 'Surf Pernambucanas',

    Operations::CLARO_CONTROLE_BOLETO   => [
        'label' => 'Claro - Controle Boleto',
        'type'  => 'Controle Boleto',
    ],
    Operations::IPLACE                  => [
        'label' =>'TradeIn - iPlace Apple'
    ],
    Operations::IPLACE_ANDROID          => [
        'label' =>'TradeIn - iPlace Android'
    ],
    Operations::IPLACE_IPAD          => [
        'label' =>'TradeIn - iPlace Ipad'
    ],
    Operations::BRUSED => [
        'label' =>'Brused'
    ],
    Operations::SALDAO_INFORMATICA => [
        'label' =>'TradeIn - Saldão Informática'
    ],
    Operations::MCAFEE_MOBILE_SECURITY => [
        'label' =>'Mobile Security - MCAFEE'
    ],
    Operations::MCAFEE_MULTI_ACCESS => [
        'label' =>'Multi Access - MCAFEE'
    ],
    Operations::CLARO_CONTROLE_FACIL    => [
        'label' => 'Claro - Controle Fácil',
        'type'  => 'Controle Fácil',
    ],
    Operations::CLARO_POS               => [
        'label' => 'Claro - Pós Pago',
        'type'  => 'Pós Pago',
    ],
    Operations::CLARO_PRE               => [
        'label' => 'Claro - Pré Pago',
        'type'  => 'Pré Pago',
    ],
    Operations::NEXTEL_CONTROLE_CARTAO  => [
        'label' => 'Nextel - Controle Cartão',
        'type'  => 'Controle Cartão',
    ],
    Operations::NEXTEL_CONTROLE_BOLETO  => [
        'label' => 'Nextel - Controle Boleto',
        'type'  => 'Controle Boleto',
    ],
    Operations::OI_CONTROLE_BOLETO      => [
        'label' => 'Oi - Controle Boleto',
        'type'  => 'Controle Boleto',
    ],
    Operations::OI_CONTROLE_CARTAO      => [
        'label' => 'Oi - Controle Cartão',
        'type'  => 'Controle Cartão',
    ],
    Operations::TIM_CONTROLE_FATURA     => [
        'label' => 'TIM - Controle Fatura',
        'type'  => 'Controle Fatura',
    ],
    Operations::TIM_EXPRESS             => [
        'label' => 'TIM - Express',
        'type'  => 'Controle Express',
    ],
    Operations::TIM_PRE_PAGO            => [
        'label' => 'TIM - Pré Pago',
        'type'  => 'Pré Pago',
    ],
    Operations::VIVO_CONTROLE_CARTAO    => [
        'label' => 'Vivo - Controle Cartão',
        'type'  => 'Controle Cartão',
    ],
    Operations::VIVO_CONTROLE           => [
        'label' => 'Vivo - Controle Boleto',
        'type'  => 'Controle Boleto',
    ],
    Operations::VIVO_POS_PAGO           => [
        'label' => 'Vivo - Pós Pago',
        'type'  => 'Pós Pago',
    ],
    Operations::VIVO_PRE                => [
        'label' => 'Vivo - Pré Pago',
        'type'  => 'Pré Pago',
    ],
    Operations::VIVO_INTERNET_MOVEL_POS => [
        'label' => 'Vivo - Internet Móvel',
        'type'  => 'Internet Móvel'
    ],
    Operations::MOVILE_CUBES => [
        'label' => 'Disney Cubes',
        'type'  => 'Disney Cubes'
    ],
    UolOperations::UOL_PLUS => [
        'label' => 'Uol - Plus',
        'type' => 'Cursos'
    ],
    UolOperations::UOL_STANDARD => [
        'label' => 'Uol - Standard',
        'type' => 'Cursos'
    ],
    UolOperations::UOL_PROFESSIONAL => [
        'label' => 'Uol - Professional',
        'type' => 'Cursos'
    ],
    Operations::CLARO_BANDA_LARGA => [
        'label' => 'Claro - Banda Larga',
        'type' => 'Banda Larga',
    ],
    Operations::CLARO_RESIDENCIAL => [
        'label' => 'Claro - Residencial',
        'type' => 'Residencial',
    ],

    Operations::SURF_PERNAMBUCANAS_PRE => [
        'label' => 'Pernambucanas - Pré Pago',
        'type' => 'Pre Pago',
    ],
    Operations::SURF_PERNAMBUCANAS_PRE_RECHARGE => [
        'label' => 'Pernambucanas - Recarga Avulsa',
        'type' => 'Pre Pago Recarga Avulsa',
    ],
    Operations::SURF_PERNAMBUCANAS_SMART_CONTROL => [
        'label' => 'Pernambucanas - Controle Inteligente',
        'type' => 'Controle Inteligente',
    ],
];

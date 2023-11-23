<?php

use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\PasswordResetStatus;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\UserStatus;

return [
    'user'  => [
        'status' => [
            UserStatus::ACTIVE       => 'Ativo',
            UserStatus::INACTIVE     => 'Inativo',
            UserStatus::NON_VERIFIED => 'Primeiro Acesso Pendente',
            UserStatus::VERIFIED     => 'Usuario Verificado',
            'description'            => [
                UserStatus::ACTIVE       => 'O usuário está cadastrado e ativo',
                UserStatus::INACTIVE     => 'O usuário está bloqueado e sem acesso a plataforma',
                UserStatus::NON_VERIFIED => 'O usuário está cadastrado, porém ainda não acessou o sistema.',
                UserStatus::VERIFIED     => 'O usuário está cadastrado e verificado, mas não ativo.',
            ]
        ]
    ],
    'reset' => [
        'status' => [
            'none'                        => 'Nada consta',
            PasswordResetStatus::APPROVED => 'aprovado',
            PasswordResetStatus::WAITING  => 'Aguardando aprovacao',
            PasswordResetStatus::REJECTED => 'Nao aprovado'
        ]
    ],
    'sale' => [
        'status' => [
            ServiceStatus::APPROVED => 'Aprovado',
            ServiceStatus::CANCELED => 'Cancelado',
            ServiceStatus::ACCEPTED => 'Em Análise'
        ]
    ],
    'operator' => [
        Operations::CLARO => [
            Operations::CLARO_PRE => 'Pré Pago',
            Operations::CLARO_CONTROLE_BOLETO => 'Controle Boleto',
            Operations::CLARO_CONTROLE_FACIL => 'Controle Fácil',
            Operations::CLARO_POS => 'Pós Pago',
            Operations::CLARO_VOZ_DADOS => 'Dep. Voz e Dados',
            Operations::CLARO_CONTROLE => 'Dep. Controle',
            Operations::CLARO_DADOS => 'Dep. Dados'
        ],
        Operations::OI => [
            Operations::OI_CONTROLE_CARTAO => 'Controle Cartão',
            Operations::OI_CONTROLE_BOLETO => 'Controle Boleto'
        ],
        Operations::VIVO => [
            Operations::VIVO_CONTROLE => 'Controle Boleto',
            Operations::VIVO_PRE => 'Pré Pago',
            Operations::VIVO_CONTROLE_CARTAO => 'Controle Cartão',
            Operations::VIVO_POS_PAGO => 'Pós Pago',
            Operations::VIVO_INTERNET_MOVEL_POS => 'Internet Móvel'
        ],
        Operations::TIM => [
            Operations::TIM_CONTROLE_FATURA => 'Controle Fatura',
            Operations::TIM_EXPRESS => 'Controle Express',
            Operations::TIM_PRE_PAGO => 'Pré Pago'
        ],
        Operations::NEXTEL => [
            Operations::NEXTEL_CONTROLE_BOLETO => 'Controle Boleto',
            Operations::NEXTEL_CONTROLE_CARTAO => 'Controle Cartão'
        ]
    ],
    'group_of_operations' => [
        GroupOfOperations::POS_PAGO => 'Pós Pago',
        GroupOfOperations::PRE_PAGO => 'Pré Pago',
        GroupOfOperations::CONTROLE => 'Controle'
    ]
];

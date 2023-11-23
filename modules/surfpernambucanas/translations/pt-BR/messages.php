<?php

use TradeAppOne\Domain\Enumerators\Operations;

return [
    'activation' => [
        Operations::SURF_PERNAMBUCANAS_PRE => 'Seu pré foi ativado! Agora é só começar a usar',
        Operations::SURF_PERNAMBUCANAS_SMART_CONTROL => 'Seu plano foi ativado! Agora é só começar a usar',
        Operations::SURF_CORREIOS_PRE => 'Seu pré foi ativado! Agora é só começar a usar',
        Operations::SURF_CORREIOS_SMART_CONTROL => 'Seu plano foi ativado! Agora é só começar a usar'
    ],
    'validation' => [
        'fromOperatorMessage' => 'A operadora doadora é obrigatório para portabilidade.'
    ]
];

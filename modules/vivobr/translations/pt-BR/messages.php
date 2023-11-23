<?php

use TradeAppOne\Domain\Enumerators\Operations;

return [
    'activation' => [
        Operations::VIVO_PRE             => 'Seu pré foi ativado! Agora é só começar a usar',
        Operations::VIVO_CONTROLE        => 'A ativação foi requerida com sucesso e a Central de Atendimentos entrará em contato em seguida para a confirmação da ativação',
        Operations::VIVO_CONTROLE_CARTAO => 'A ativação foi requerida com sucesso e a Central de Atendimentos entrará em contato em seguida para a confirmação da ativação',
        Operations::VIVO_POS_PAGO        => 'A ativação foi requerida com sucesso e a Central de Atendimentos entrará em contato em seguida para a confirmação da ativação',
        'with_biometrics' => 'Coleta de biometria necessária para a conclusão da ativação'
    ],
    'apiSun' => [
        'totalization' => [
            'true'  => ':name, você Possui serviço fixa ativo.',
            'false' => ':name, você Não Possui serviço fixa ativo.'
        ]
    ]
];

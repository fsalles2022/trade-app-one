<?php

return [
    'activation'       => [
        'claro_pre' => [
            'message' => 'Seu pré está ativo! Agora é só começar a usar'
        ],
        'claro_pos' => [
            'message' => ''
        ],
        'check_sale_failed' => [
            'message' => 'Venda já registrada anteriormente com os dados informados.'
        ],
        'save_sale_failed' => [
            'message' => 'Ocorreu um erro ao salvar a venda. Tente novamente mais tarde.'
        ]
    ],
    'score'            => [
        'no_score' => 'Cliente não tem score suficiente, tente oferecer um plano Controle Fácil',
    ],
    'NUMERO_NAO_ATIVO' => 'A linha nao pode contratar o plano pois já é um CONTROLE, PÓS PAGO, se for uma nova linha então ainda não reconhecido pela Claro, AGUARDE 20 minutos antes de prosseguir',
    'iccid' => [
        'not_promoter' => 'Usuário não elegível para consulta de SimCard',
        'min_length'   => 'Devem ser fornecidos ao menos 6 caracteres para realizar a busca'
    ],
    'automaticRegistration' => [
        'success' => 'Usuario cadastrado com sucesso.',
        'received' => 'Pedido de inclusão de usuário recebido com sucesso. Em breve você pode consultar pelo protocolo a inclusão.',
        'notFound' => 'Usuário não encontrado em nossa base de dados. Por favor verifique os dados enviados e tente novamente!',
    ]
];

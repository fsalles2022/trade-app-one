<?php

return [
    'planDetails' => [
        'internet'      => 'Franquia de dados - :internet',
        'bonusInternet' => 'Bonus Internet - :bonusInternet',
        'voice'         => 'Franquia de voz :voice',
        'fee'           => 'GANHE MAIS GIGA a cada :period meses!!! O melhor é que o valor da sua conta não muda por 2 anos! ',
    ],
    'activation'  => [
        'success' => 'Parabéns! Seu pedido foi realizado com sucesso e sua linha será ativada em até 24 horas. Você receberá um SMS e deve responder para confirmar a contratação.'
    ],
    'eligibility' => [
        'emptyPlans' => 'Não existem planos elegíveis para este DDD e cliente'
    ],
    'bank_data' => [
        'success' => 'Dados bancários validados com sucesso'
    ],
    '1000'        => [
        '100'  => 'Falha na transação. Não foi possível gerar o protocolo. Realize novo processo de venda.',
        '101'  => 'Já foi feita uma ativação anteriormente com os mesmo dados informados, por favor refaça a venda!',
        '200'  => 'Ocorreu um erro ao consultar o perfil do cliente, por favor repita a etapa anterior.',
        '201'  => 'Cliente sem score suficiente para aquisição do plano. Para esclarecimentos, ligue no 1050 de qualquer telefone ou *611 (do seu Nextel).',
        '202'  => 'Falha na transação. Não foi possível realizar a análise de crédito do cliente, por favor refaça a venda',
        '300'  => 'CPF possui bloqueio administrativo na operadora. Ligue para 1050 de qualquer telefone ou *611 (do seu Nextel) para que você possa saber se pode prosseguir com a Ativação de forma segura.',
        '301'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '302'  => 'CPF possui bloqueio junto a operadora. Ligue para 1050 de qualquer telefone ou *611 (do seu Nextel) para que você possa saber se pode prosseguir com a Ativação de forma segura.',
        '400'  => 'Falha na validação dos dados do cliente, por favor refaça a venda.',
        '401'  => 'Não foi possível validar o CPF do cliente. Realize novo processo de venda.',
        '500'  => 'Falha ao gerar o número de linha para ativação, por favor refaça a venda.',
        '600'  => 'Já existe um processo de pré-análise para o número deste cliente.',
        '700'  => 'Falha ao validar a Conta Corrente do banco, por favor refaça a venda.',
        '701'  => 'Falha ao validar os dados bancários, por favor refaça a venda.',
        '800'  => 'Não foi possível validar a Data de Vencimento do controle, por favor refaça a venda',
        '801'  => 'A operadora do cartão de crédito não está nos respondendo no momento. Aguarde uns minutos e clique novamente em ativar. Caso o operadora do cartão não responda as solicitações, sugira ao cliente um controle boleto.',
        '901'  => 'Iccid Inválido: erro de digitação, CHIP já utilizado, danificado ou expirado. Realize novo processo de venda com outro CHIP.',
        '1200' => 'Houve uma instabilidade na operadora, refaça o processo de venda.',
    ],
    '2000'        => [
        '101'  => 'Já foi feita uma ativação anteriormente, por favor refaça a venda para poder ativar!',
        '200'  => 'Ocorreu um erro ao consultar o perfil do cliente, por favor repita a etapa anterior.',
        '201'  => 'Cliente sem score suficiente para aquisição do plano. Tente novamente ou altere a forma de pagamento.',
        '202'  => 'Falha na transação. Não foi possível realizar a análise de crédito do cliente, por favor refaça a venda',
        '300'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '301'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '302'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '400'  => 'Falha na validação dos dados do cliente, por favor refaça a venda.',
        '401'  => 'Não foi possível validar o CPF do cliente. Realize novo processo de venda.',
        '500'  => 'Falha ao gerar o número de linha para ativação, por favor refaça a venda.',
        '600'  => 'Já existe um processo de elegibilidade para o número deste cliente.',
        '700'  => 'Falha ao validar a Conta Corrente do banco, por favor refaça a venda.',
        '701'  => 'Falha ao validar os dados bancários, por favor refaça a venda.',
        '800'  => 'Não foi possível validar a Data de Vencimento do controle, por favor refaça a venda',
        '801'  => 'A operadora do cartão de crédito não está nos respondendo no momento. Aguarde uns minutos e clique novamente em ativar. Caso o operadora do cartão não responda as solicitações, sugira ao cliente um controle boleto.',
        '901'  => 'Iccid Inválido: erro de digitação, CHIP já utilizado, danificado ou expirado. Realize novo processo de venda com outro CHIP.',
        '1200' => 'A operadora está com uma oscilação. Aguarde 2 minutos e tente novamente.',
    ],
    '3000'        => [
        '101'  => 'Já foi feita uma ativação anteriormente, por favor refaça a venda para poder ativar!',
        '200'  => 'Ocorreu um erro ao consultar o perfil do cliente, por favor repita a etapa anterior.',
        '201'  => 'Cliente sem score suficiente para aquisição do plano. Tente novamente ou altere a forma de pagamento.',
        '202'  => 'Falha na transação. Não foi possível realizar a análise de crédito do cliente, por favor refaça a venda',
        '300'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '301'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '302'  => 'Problema na fase de análise do crivo, refaça o processo de venda.',
        '400'  => 'Falha na validação dos dados do cliente, por favor refaça a venda.',
        '401'  => 'Não foi possível validar o CPF do cliente. Realize novo processo de venda.',
        '500'  => 'Falha ao gerar o número de linha para ativação, por favor refaça a venda.',
        '600'  => 'Já existe um processo de elegibilidade para o número deste cliente.',
        '700'  => 'Dados bancários inválidos, por favor, verifique e tente novamente.',
        '701'  => 'Falha ao validar os dados bancários, por favor refaça a venda.',
        '800'  => 'Não foi possível validar a Data de Vencimento do controle, por favor refaça a venda',
        '801'  => 'A operadora do cartão de crédito não está nos respondendo no momento. Aguarde uns minutos e clique novamente em ativar. Caso o operadora do cartão não responda as solicitações, sugira ao cliente um controle boleto.',
        '901'  => 'Iccid Inválido: erro de digitação, CHIP já utilizado, danificado ou expirado. Realize novo processo de venda com outro CHIP.',
        '1200' => 'A operadora está com uma oscilação. Aguarde 2 minutos e tente novamente.',
    ]
];

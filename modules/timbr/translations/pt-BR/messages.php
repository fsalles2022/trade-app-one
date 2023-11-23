<?php

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Modes;
use TimBR\Enumerators\TimBRSegments;

return [
    'eligibility'    => [
        'success'                       => 'Elegibilidade concluída com sucesso',
        'empty_response'                => 'Ops! Ocorreu um erro desconhecido no processo de Elegibilidade na operadora.',
        Operations::TIM_BLACK => [
            'ineligible' => 'Cliente não possui crédito disponível para contratar o plano selecionado.'
        ],
        Operations::TIM_BLACK_MULTI     => [
            'ineligible' => 'Cliente não possui crédito disponível para contratar o plano selecionado.'
        ],
        Operations::TIM_BLACK_MULTI_DEPENDENT => [
            'ineligible' => 'Cliente não possui crédito disponível para contratar o plano selecionado.'
        ],
        Operations::TIM_BLACK_EXPRESS   => [
            'ineligible' => 'Cliente não possui crédito disponível para contratar o plano selecionado.'
        ],
        Operations::TIM_CONTROLE_FATURA => [
            'ineligible' => 'O cliente não possui crédito suficiente. Mas você ainda pode oferecer um plano do tipo Express com cartão de crédito!'
        ],
        Operations::TIM_EXPRESS         => [
            'ineligible' => 'O cliente não possui crédito suficiente. Mas você ainda pode oferecer um plano do tipo Fatura!'
        ],
        Operations::TIM_PRE_PAGO        => [
            'ineligible' => 'Planos Pre Pago não estão liberados no seu seu DDD, não será possível continuar a venda.'
        ],
        'not_found'                     => 'Consulta de pré-análise expirada, volte ao início e realize uma nova pré-análise.',
        'plan_loyalty'                  => 'Fidelização de Plano',
        'device_loyalty'                => 'Fidelização de Aparelho',
        'plan_device_loyalty'           => 'Fidelização de Plano e Aparelho',
    ],
    'sim_card_activation' => [
        'success' => 'Alocado um número para o SimCard TIM com sucesso',
    ],
    'authentication' => [
        'failed'           => 'Problemas no cadastro de vendedor, entre em contato com o suporte para atualização e regularização para poder continuar a venda na operadora Tim',
        'bearer_not_found' => 'Usuário TIM não disponível, tente novamente',
    ],
    'express'        => [
        'acceptance'  => 'Parabéns! A ativação do Controle Express foi concluída. Faça ligação de 10 segundos com esse número, para concluir o processo.',
        'instability' => 'Ocorreu uma instabilidade ao gerar pedido na Tim, por favor refaça a venda.',
    ],
    'flex' => [
        'success' => 'Parabéns! seu Controle Flex foi ativo com sucesso'
    ],
    'acceptance'     => [
        'prepago' => 'Parabéns! seu Pré Pago foi ativo com sucesso',
        '11'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '12'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '13'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '14'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '15'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '16'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '17'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '18'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '19'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '21'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '22'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '24'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '27'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '28'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '31'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '32'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '33'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '34'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '35'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '37'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '38'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '41'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '42'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '43'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '44'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '45'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '46'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '47'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '48'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '49'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '51'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '53'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '54'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '55'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '61'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '62'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '63'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '64'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '65'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '66'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '67'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '68'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '69'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '71'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '73'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '74'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '75'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '77'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '79'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '81'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '82'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '83'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '84'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '85'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '86'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '88'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '89'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800 e peça para o cliente confirmar a compra do plano Controle.',
        '87'      => 'Parabéns! A ativação do Controle Fatura foi solicitada. Para finalização do pedido, ligue do número ativo para o telefone 0800 5805 800  e peça para o cliente confirmar a compra do plano Controle.',
        '91'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '92'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '93'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '94'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '95'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '96'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '97'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '98'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
        '99'      => 'Parabéns! A ativação do  Controle Fatura foi solicitada. Para finalização do pedido, o cliente receberá uma ligação da TIM para confirmar a venda. Informe ao cliente que essa ligação ocorrerá em até 10 minutos. Caso a TIM não consiga contato nesse período novas tentativas serão realizadas em até 7 dias.',
    ],
    'cep'            => [
        'not_found' => 'Por favor verifique se o CEP está correto, em caso de dúvida solicite um comprovante de endereço para conferir.'
    ],
    'brScan' => [
        'sale_term' => [
            Modes::ACTIVATION => 'Ativação',
            Modes::PORTABILITY => 'Portabilidade',
            Modes::MIGRATION => 'Migração',
            Operations::TIM_CONTROLE_FATURA => 'Controle Fatura',
            Operations::TIM_BLACK => 'Black',
            Operations::TIM_BLACK_EXPRESS => 'Black Express',
            Operations::TIM_BLACK_MULTI => 'Black Multi',
            Operations::TIM_BLACK_MULTI_DEPENDENT => 'Black Multi Dependente',
            TimBRSegments::CONTROLE => 'Controle',
            TimBRSegments::POS_PAGO => 'Pos Pago',
            TimBRSegments::POS_EXPRESS => 'Pos Express',
            TimBRSegments::DIGITALPOS => 'Digital Pos',
            'dependent' => 'Dependente',
        ]
    ]
];

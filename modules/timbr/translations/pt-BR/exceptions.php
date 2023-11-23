<?php

return [
    'identifiers'                       => [
        'point_of_sale_not_found' => 'Identificador do PDV na operadora Tim não encontrado, solicite ao seu gestor para entrar em contato com o chat.',
        'network_not_found'       => 'Identificador da rede na operadora Tim não encontrado',
        'sergeant_not_found'      => 'Solicite revisão do cadastro de usuário sargento'
    ],
    'cep'                               => [
        'not_found' => 'CEP não encontrado ou retorno incompleto da API de CEP da operadora'
    ],
    'm4u'                               => [
        'adapter' => 'Não foi possivel concluir o processo na requisição para a m4u'
    ],
    'authentication'                    => [
        'code_not_found'   => 'Não foi possível gerar o código para autenticação do usuário',
        'failed'           => 'Cadastro do vendedor Inativo ou com Bloqueio Administrativo. Fale com o chat para análise do seu perfil.',
        'bearer_not_found' => 'Falha na extracao do usuário Tim',
    ],
    'eligibility'                       => [
        'area_code_validation' => 'Não é possível realizar uma venda para um DDD ou CEP diferente da sua região. Caso tenha inserido dados divergentes, refaça a venda.',
        'request_adapter'      => 'Não foi possível gerar a solicitação de elegibilidade',
        'not_found'            => 'O tempo para concluir a venda expirou, volte ao início.',
    ],
    'TimBRAuthenticationCookieNotFound' => [
        'message' => 'Cadastro do vendedor Inativo ou com Bloqueio Administrativo. Fale com o chat para análise do seu perfil.'
    ],
    'TimBRAuthenticationInvalidCookies' => [
        'message' => 'Cookies de autenticação invalidos. Entre em contado com o adminitrador do sistema.'
    ],
    'TimBRInvalidProduct'               => [
        'message' => 'Produto não encontrado'
    ],
    'TimBRInvalidDevice'               => [
        'message' => 'Aparelho não encontrado'
    ],
    'TimBREncriptCPFException'          => [
        'message'     => 'Aconteceu uma instabilidade na operadora, tente novamente',
        'description' => 'Aconteceu uma instabilidade momentânea no processo de autorização do vendedor na TIM. Tente novamente dentro de alguns segundos'
    ],
    'InvalidTimCode'                    => [
        'message'     => 'Identificador da Tim inválido',
        'description' => 'Verifique se tem caracteres alfanuméricos sem caracteres especiais além do "_" e sem espaços'
    ],
    'timPre' => [
        'message'     => 'DDD inválido para o plano Pré-Pago',
        'description' => "Não é possível realizar uma venda de Pré-Pago para o DDD :ddd"
    ],
    'brScan' => [
        'generate_link_fail' => 'Não foi possível gerar o link da biometria',
        'generate_sale_term_fail' => 'Não foi possível enviar o termo para a BRScan',
        'sale_term_fail' => 'Não foi possível consultar o status do termo na BRScan',
    ],
    'TimBRRebateImport' => [
        'invalid_product' => 'Nome ou código do produto TIM é inválido',
    ]
];

<?php

return [
    'third_party_unavailable'              => 'Já estamos trabalhando para solucionar, o help desk esta de prontidão',
    'siv_invalid_credentials'              => 'Atualize suas credenciais, 3 tentativas erradas bloquearão sua senha',
    'no_access_to_siv'                     => 'Solicite cadastro no sistema do Siv para ter acesso',
    'sale_no_exists'                       => 'Verifique se o número da transação esta correto, é um número composto por 16 dígitos exemplo: 2018031515524786',
    'service_no_exists'                    => 'Verifique se o número esta correto, é um número composto por 16 dígitos seguido por 3 digitos de identificacao exemplo: 2018031515524786-000',
    'service_non_integrated'               => 'Faça a integração com metodo put : sale/ antes de ativar',
    'service_not_available_for_contest'    => 'O serviço não está disponível para contestar.',
    'operator_not_found'                   => 'Verifique o nome da Operadora para poder o serviço correto',
    'user_doesnt_belongs_to_point_of_sale' => 'Verifique o idenficador do ponto de venda que enviou',
    'protocol_no_exists'                   => 'Verifique se o número do protocolo existe para a venda que enviou',
    'tim_status_no_exists'                 => 'Verifique o retorno do endpoint da TIM',
    'user'                                 => [
        'not_found' => 'É necessário enviar um Usuário existente.',
        'already_has_active_reset_request' => 'É necessário esperar seu gerente aprovar sua solicitação',
        'invalid_date_of_birth' => 'É necessário enviar uma data de nascimento válida, utilize nosso modelo'
    ],
    'pos'                                  => [
        'not_associated_with_user' => 'É necessário enviar um CNPJ que esteja vinculado ao seu cadastro',
        'network_no_exists' => 'É necessário enviar uma rede existente para vincular ao Ponto de Venda!',
        'not_found'         => 'É necessário enviar um Ponto de Venda existente.'
    ],
    'role'                                 => [
        'not_found' => 'É necessário enviar uma Função existente.',
        'dont_have_indication_permission' => 'É necessário atribuir permissão para indicar colaborador.'
    ],
    'network'                              => [
        'not_found' => 'É necessário enviar uma Rede existente.'
    ],
    'sun'                                  => [
        'error' => 'Ocorreu um erro de comunicação interno. Por favor, tente novamente!'
    ],
    'sign_in'                              => [
        'exceeded_sign_in_attempts' => 'Solicite o a redefinição de senha'
    ],
    'importable'                           => [
        'column_not_found' => 'Verifique se o arquivo tem a coluna requisitada ou baixe nosso modelo'
    ]
];

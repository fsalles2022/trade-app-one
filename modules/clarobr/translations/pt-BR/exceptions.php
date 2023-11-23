<?php

use ClaroBR\Exceptions\ClaroExceptions;
use ClaroBR\Exceptions\Siv3Exceptions;
use ClaroBR\Exceptions\SivAuthExceptions;
use ClaroBR\Exceptions\SivAutomaticRegistrationExceptions;

return [
    ClaroExceptions::REBATE_WITH_INVALID_STRUCTURE => 'Não foram encontrados planos para este aparelho, volte ao passo anterior e verifique o aparelho.',
    ClaroExceptions::CONTEST_INVALID_RESPONSE      => 'Não foi possível contestar a venda no momento.',
    SivAuthExceptions::SEND_PROMOTER_TOKEN         => 'Promotor não autenticado, envie o token.',
    SivAuthExceptions::FIRST_ACCESS_PROMOTER       => 'Promotor não autenticado, envie o token e o cpf.',
    SivAuthExceptions::SIV_USER_ALREADY_LOGGED     => 'CPF incorreto, verifique se o mesmo corresponde ao centralizador.',
    SivAuthExceptions::DEFAULT                     => 'Usuário não localizado, verifique seu cadastro.',
    SivAuthExceptions::SELECT_PDV                  => 'Escolha um PDV',
    SivAuthExceptions::TOKEN_NOT_FOUND             => 'Ocorreu um erro ao obter o token de autenticação.',
    SivAuthExceptions::SIV_USER_NOT_FOUND          => 'CPF incorreto, verifique seu cadastro no SIV',
    SivAuthExceptions::SIV_POINT_OF_SALE_NOT_FOUND => 'PDV não está cadastrado, entre em contato com o suporte.',
    ClaroExceptions::UPDATE_ERROR                  => 'Ocorreu um erro ao atualizar o ICCID.',
    ClaroExceptions::AUTHENTICATE_WITHOUT_POINTOFSALE_CODE => 'Autentica não pode ser realizada, pois o PDV está incompleto.',
    ClaroExceptions::BR_SCAN_INVALID_RESPONSE      => 'Ocorreu um erro ao validar o autentica.',
    ClaroExceptions::PAYMENT_URL_NOT_FOUND         => 'Url de pagamento não localizada.',
    Siv3Exceptions::ADDRESS_NOT_FOUND              => 'Endereço não encontrado.',
    Siv3Exceptions::INVALID_CREDENTIALS            => 'Não foi possível autenticar no serviço.',
    Siv3Exceptions::UNAVAILABLE_SERVICE            => 'Não foi possível consultar o serviço, tente novamente.',
    Siv3Exceptions::INVALID_CODE                   => 'Atenção, o código digitado está incorreto, favor verifique e tente novamente.',
    Siv3Exceptions::UNAUTHORIZED_OPERATION         => 'Operação não autorizada para consultar o serviço',

    'credentials'                       => [
        'invalid' => 'Entre em contato com o Gerente de Negócios da Claro para saber o motivo e LIBERAR seu usuário.'
    ],
    'ClaroBRProvisionalNumberException' => [
        'message' => 'Não foi possível gerar um número provisório'
    ],
    'ResponseEmptyException'            => [
        'message'     => 'Resposta invalida da operadora por instabilidade, por favor tente mais tarde',
        'description' => 'Resposta invalida da operadora por instabilidade, por favor tente mais tarde',
    ],
    'AttributeNotFound'                 => [
        'message'     => 'Atributo :attribute não encontrado',
        'description' => 'Atributo durante o rebate'
    ],
    'PlansNotFoundException'            => [
        'message' => 'Não existem planos disponíveis.'
    ],
    'RebateNotFound'                    => [
        'message' => 'Não foi possível validar o rebate. Entre em contato com o chat.'
    ],
    'InvalidClaroCode'                  => [
        'message' => 'Identificador da Claro invalido.'
    ],
    'AutomaticRegistration' => [
        SivAutomaticRegistrationExceptions::USER_ALREADY_EXISTS => 'Usuário já cadastrado em nossa base de dados.',
        SivAutomaticRegistrationExceptions::INVALID_SIV_OPERATION => 'Operacão do PDV deve ser: OUT ou ESTRUTURAL',
        SivAutomaticRegistrationExceptions::NOT_HAVE_ROLES_FROM_USER => 'Não existem funções adequadas para o cadastro',
        SivAutomaticRegistrationExceptions::USER_NOT_BE_CREATED => 'Ocorreu um erro ao cadastrar o usuário.'
    ]
];

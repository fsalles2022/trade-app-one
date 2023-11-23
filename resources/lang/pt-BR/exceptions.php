<?php

use Buyback\Exceptions\TradeInExceptions;
use Reports\Exceptions\ReportExceptions;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Exceptions\ImportableExceptions;
use TradeAppOne\Exceptions\RemotePaymentException;
use TradeAppOne\Exceptions\SystemExceptions\DateExceptions;
use TradeAppOne\Exceptions\SystemExceptions\HierarchyExceptions;
use TradeAppOne\Exceptions\SystemExceptions\ImportHistoryExceptions;
use TradeAppOne\Exceptions\SystemExceptions\NetworkExceptions;
use TradeAppOne\Exceptions\SystemExceptions\OiResidentialSaleImportableExceptions;
use TradeAppOne\Exceptions\SystemExceptions\PointOfSaleExceptions;
use TradeAppOne\Exceptions\SystemExceptions\SaleExceptions;
use TradeAppOne\Exceptions\SystemExceptions\ServiceOptionsExceptions;
use TradeAppOne\Exceptions\SystemExceptions\S3Exceptions;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

return [
    ImportableExceptions::REGISTER_ALREADY_EXISTS         => 'Registro ja existente',
    ImportableExceptions::USER_CANNOT_ADD_TO_NETWORK      => 'Nao e possivel importar para esta rede, verifique a coluna identificador da rede',
    TradeInExceptions::IMEI_ALREADY_EXISTS                => 'Já existe uma venda vinculada a este Imei',
    ReportExceptions::FAILED_REPORT_BUILD                 => 'Não foi possível concluir a extração do relatório analítico.',
    ReportExceptions::REPORT_FILTERS_REQUESTED            => 'Necessário informar filtros para exportar.',
    'failed_report_build'                                 => 'Não foi possível concluir a extração do relatório analítico.',
    'third_party_unavailable'                             => 'Parceiro :service esta fora do ar neste momento',
    'siv_invalid_credentials'                             => 'Credenciais inválidas',
    'no_access_to_siv'                                    => 'Infelizmente você não possui acesso aos serviços da Claro',
    'point_of_sale_without_siv'                           => 'O ponto de venda selecionado nao tem credenciais do Siv',
    'sale_no_exists'                                      => 'Esta transação de venda não existe',
    'service_no_exists'                                   => 'Este serviço não existe',
    'invalid_service_status'                              => ':status não é um status válido para o serviço',
    'service_non_integrated'                              => 'O serviço selecionado ainda não foi integrado',
    'service_not_available_for_contest'                   => 'O serviço não está disponível para contestar.',
    'protocol_no_exists'                                  => 'Não existe número de protocolo para esta venda',
    'tim_status_no_exists'                                => 'Status no endpoint da TIM retornou vazio',
    'operator_not_found'                                  => 'Operadora não encontrada no nosso portfólio',
    'user_doesnt_belongs_to_point_of_sale'                => 'Você não está cadastrado neste ponto de venda',
    'internal_error'                                      => 'Ocorreu um erro interno ao processar a solicitação, por favor, tente novamente.',
    RemotePaymentException::INVALID_SERVICE_PAYMENT_TOKEN => 'Token de pagamento do serviço inválido.',
    RemotePaymentException::PAYMENT_URL_NOT_CREATED       => 'Não foi possivel criar a url de pagamento, Verifique os dados do serviço.',
    'user'                                           => [
        'not_found'                                           => 'Usuário não encontrado!',
        'already_has_active_reset_request'                    => 'Aguarde seu gerente aprovar sua solicitação',
        'invalid_date_of_birth'                               => 'Data de nascimento inválida',
        UserExceptions::UNAUTHORIZED                          => 'Usuario nao possui permissoes necessarias para acessar este recurso',
        UserExceptions::NOT_PERMISSION_UNDER_ROLE             => 'Você não tem autorização sobre a função :role',
        UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_USER      => 'Você não tem autorização sobre este usuario.',
        UserExceptions::NOT_BELONGS_TO_POINT_OF_SALE          => 'Você não tem autorização sobre o ponto de venda informado',
        UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_HIERARCHY => 'Você não tem autorização sobre a hierarquia informada',
        UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_NETWORK   => 'Usuário não tem autorização sobre a rede.',
        UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_QUIZ      => 'Usuário não tem autorização sobre este questionario.',
        UserExceptions::NO_NETWORK                            => 'Usuário não está cadastrado em nenhuma rede'
    ],
    'pos' => [
        'not_associated_with_user'                 => 'Este ponto de venda não está associado ao seu cadastro',
        'network_no_exists'                        => 'Não há serviços vinculados ao PDV e REDE para habilitar a opção selecionada.',
        'not_found'                                => 'Ponto de venda inexistente',
        PointOfSaleExceptions::CNPJ_ALREADY_EXISTS => 'CNPJ já vinculado a um ponto de venda.'
    ],
    'network' => [
        'not_found'                                     => 'Rede não encontrada!',
        'not_associated_with_user'                      => 'Essa rede não está associada ao seu cadastro',
        NetworkExceptions::NOT_BELONGS_TO_POINT_OF_SALE => 'A Função e o Ponto de venda devem pertencer a mesma rede',
        NetworkExceptions::NOT_BELONGS_TO_HIERARCHY     => 'A Função e a Regional devem pertencer a mesma rede',
        NetworkExceptions::AVAILABLE_SERVICE_NOT_FOUND  => 'Tipo de serviço não localizado',
        NetworkExceptions::CHANNEL_NOT_FOUND            => 'Canal não localizado',
        NetworkExceptions::SLUG_ALREADY_EXISTS          => 'Slug já existe.',
        NetworkExceptions::AVAILABLE_SERVICES_EMPTY     => 'O usuário não possui serviços disponiveis.'
    ],
    'role' => [
        'not_found'                       => 'Função não encontrada!',
        'role_exists'                     => 'A função já existe em nossa base de dados, por favor, atribua outro nome.',
        'assign_permission'               => 'Você não possui a permissão :permission, então não é possível atribuí-la a função a ser criada.',
        'not_can_add_in_network'          => 'Você não pode atribuir ou editar funções para esta rede',
        'has_not_authority_under_role'    => 'Você não tem permissão sob está função',
        'can_not_add_parent'              => 'Não é possivel definir esta hierarquia para a função, você não tem permissões sob ela.',
        'not_belongs_to_network'          => 'A hierarquia informada (ou você) não pertence a esta rede.',
        'dont_have_indication_permission' => 'Sem permissão para indicar colaborador.'
    ],
    'general' => [
        'model_invalid'   => 'Atributos inválidos para este modelo',
        'service_invalid' => 'Service inexistente!',
    ],
    'third_party' => [
        'default' => 'Não foi possível concluir o processo',
    ],
    'assistance' => [
        'not_found' => 'Não foi possível concluir o processo'
    ],
    'importable' => [
        'not_found'             => 'Não existe metodos para importar estes dados',
        'column_not_found'      => 'Coluna :column não encontrada',
        'file_uploaded_invalid' => 'O arquivo enviado é inválido'
    ],
    'sign_in' => [
        'exceeded_sign_in_attempts' => 'Tentativas de login excedidas, solicite o reset da sua senha.',
        'status'                    => [
            UserStatus::INACTIVE     => 'Credenciais inativas.',
            UserStatus::NON_VERIFIED => 'Credenciais inativas.',
            UserStatus::VERIFIED     => 'Credenciais inativas.',
        ]
    ],
    'no_database' => [
        'message' => 'Não conseguimos buscar essa informação no momento.'
    ],

    'error_sending_email' => [
        'message' => 'Erro ao enviar emails, contate o suporte.'
    ],
    'point_of_sale_state_not_found' => [
        'message' => 'Solicite atualização do cadastro de DDD do Ponto de Venda.'
    ],
    'empty_plans' => [
        'message' => 'Não existem planos disponíveis.'
    ],
    'service' => [
        'canot_be_contested'                        => 'Este serviço não pode ser contestado no momento. Aguarde a atualização de status',
        'status_change_success'                     => 'Status alterado com sucesso!',
        'status_change_error'                       => 'Ocorreu um problema na alteração do Status',
        'change_success'                            => 'Alterado com sucesso!',
        ServiceExceptions::CANNOT_BE_CANCEL         => 'Este serviço não pode ser cancelado.',
        ServiceExceptions::ACTIVE_TO_CANCEL         => 'Não possivel cancelar um serviço que não está aprovado.',
        ServiceExceptions::CANCELLATION_EXPIRED     => 'Não foi possivel cancelar, o prazo de 7 dias para cancelamento expirou.',
        ServiceExceptions::NOT_FOUND                => 'Serviço não encontrado',
        ServiceExceptions::ALREADY_CANCELLED        => ':serviceType cancelado.',
        ServiceExceptions::NEEDS_ACCEPTED_TO_CANCEL => 'Para alterar o serviço é necessário que o status esteja em análise, status atual é :status',
        ServiceExceptions::TOKEN_CARD_NOT_FOUND     => 'Token do cartão de crédito não foi encontrado, por favor, tente novamente ou contate o suporte.'
    ],
    'date' => [
        DateExceptions::FORMAT_INCORRECT => 'Data: :date incorreta'
    ],
    'hierarchy' => [
        HierarchyExceptions::NOT_FOUND => 'Hierarquia não encontrada'
    ],
    'import_history' => [
        ImportHistoryExceptions::FILE_NOT_FOND => 'Arquivo não encontrado',
        HierarchyExceptions::NOT_FOUND         => 'Hierarquia não encontrada',
        HierarchyExceptions::WITHOUT_NETWORK   => 'Hierarquia não pertence a nenhuma rede.'
    ],
    'userImportable' => [
        'noHierarchyAndPdv' => 'Necessario informar um ponto de venda, regional ou os dois.',
        'cpfEmpty'          => 'Necessário informar o CPF.'
    ],
    'oiResidentialImportable' => [
        OiResidentialSaleImportableExceptions::SALESMAN_NOT_FOUND => 'Vendedor não encontrado',
        OiResidentialSaleImportableExceptions::SALE_ALREADY_EXISTS => 'A venda já existe em nossa base de dados.'
    ],
    's3' => [
        S3Exceptions::PUT_ERROR      => 'Ocorreu um erro ao salvar o arquivo.',
        S3Exceptions::DELETE_ERROR   => 'Ocorreu um erro ao deletar o arquivo.',
        S3Exceptions::DOWNLOAD_ERROR => 'Ocorreu um erro no download do arquivo.',
        S3Exceptions::CONFIG_ERROR   => 'Ocorreu um erro ao configurar o client S3.',
        'status_false'               => 'S3 Storage Status is False'
    ],
    'serviceOptions' => [
        ServiceOptionsExceptions::SERVICE_OPTIONS_OPERATIONS_NOT_AVAILABLE => 'Opção selecionada não disponível no momento.',
        ServiceOptionsExceptions::ACTION_SERVICE_OPTIONS_NOT_FOUND => 'Opção de serviço não localizada.'
    ]
];

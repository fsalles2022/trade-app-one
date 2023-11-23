<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'role' => [
        'not_created' => 'Não foi possível criar a nova Função!',
        'no_permission' => 'Você não possui permissões para editar ou cadastrar funções.',
        'created' => 'Função criada com sucesso!',
        'edited' => 'Função editada com sucesso!'
    ],
    'failed' => 'Essas credenciais não conferem com nossos registros',
    'throttle' => 'Muitas tentativas incorretas. Por favor, tente novamente em :seconds segundos',
    'token_created' => 'Token criado com sucesso',
    'token_invalid_credentials' => 'Esqueceu sua senha? Solicite o reset!',
    'token_not_created' => 'Token não criado',
    'signout' => 'Usuário deslogado',
    'user_confirmed' => 'Usuario verificado, aguarde aprovação',
    'not_authorized' => 'Sem permissões necessárias',
    'session_expired' => 'Credenciais inválidas',
    'credentials_third_party_success' => 'Credenciais adicionadas ao perfil',
    'credentials_third_party_failed' => 'Credenciais inválidas',
    'multiple_access' => 'Múltiplos logins detectados. Necessário reautenticar',

    /*
    |--------------------------------------------------------------------------
    | Password Recovery Language Lines
    |--------------------------------------------------------------------------
    */

    'password' => 'Senha deve ter pelo menos seis caracteres e corresponder a senha de confirmação.',
    'password_updated' => 'Sua senha foi restaurada!',
    'password_updated_error' => 'Ocorreu um erro ao atualizar sua senha.',
    'request_recovery_sent' => 'Nós enviamos um pedido para restaurar sua senha para seu gerente!',
    'request_recovery_duplicated' => 'Um token de verificação já foi criado para este usuário.',
    'request_recovery_error' => 'Ocorreu um erro ao gerar o token de verificação de reset de senha.',
    'request_approved' => 'Pedido aceito com sucesso!',
    'request_rejected' => 'Pedido recusado com sucesso!',
    'cant_recovery_password' => 'Não é possível realizar esta operação',
    'user_or_role_not_found' => 'Usuário ou Papel não encontrado',
    'user_allocated' => 'Usuário realocado com sucesso',
    'user_cant_be_allocated' => 'Usuário não pode ser realocado',
    'user_created' => 'Usuário cadastrado com sucesso',
    'user_updated' => 'Usuário atualizado com sucesso',
    'user_update_error' => 'Ocorreu um erro na atualização do usuário',
    'user_creating_error' => 'Ocorreu um erro ao cadastrar o usuário',
    'user_show_error' => 'Usuário não existe ou você não possui permissões para visualizá-lo',
    'created_email_verification_sent' => 'Verifique a a caixa de entrada, enviamos o link para confirmar a conta',
    'invalid_verification_code' => 'Código de verificação inválido',
    'valid_verification_code' => 'Código de verificação válido',
    'network_no_exists' => 'Rede inexistente',
    'network_edited' => 'Rede editada com sucesso',
    'network_create' => 'Rede criada com sucesso',
    'hierarchy_create' => 'Hierarquia criada com sucesso',
    'network_create_error' => 'Ocorreu um erro ao criar rede',
    'sale_saved' => 'Venda salva com sucesso',
    'sale_updated' => 'Venda atualizada com sucesso',
    'sale_duplicated' => 'Venda já existe',
    'sale_not_saved' => 'Não foi possível terminar o processo',
    'default' => 'Não foi possível terminar o processo',
    'default_success' => 'Concluído com sucesso',
    'user' => [
        'first_access' => 'Notamos que esse é o seu primeiro acesso, então você precisará escolher uma senha antes de continuar',
        'activated' => 'Ativação concluída',
        'has_no_verification_code' => 'Código de verificação inválido',
        'first_six_digits_of_cpf' => '6 primeiros dígitos do CPF',
    ],

    '404' => 'Recurso não encontrado',
    'ServiceAlreadyInProgress' => 'Esta venda não permite mais um processo de ativação, pois está :status',
    'dispatch_mail' => 'Sucesso, o email foi colocado em fila e será enviado em breve!',
    'contest' => ['success' => 'A contestação resultou no status :status'],
    'preSale' => [
        'success' => 'Pré venda atualizada com sucesso.',
        'error' => 'Ocorreu um erro ao atualizar a pré venda',
        'notFound' => 'Pré venda não localizada.'
    ],
    'backoffice' => [
        'success_save_comment' => 'Comentario salvo com sucesso'
    ],

    'pointOfSale' => [
        'pointOfSale_updated' => 'Ponto de venda atualizado com sucesso',
        'pointOfSale_created' => 'Ponto de venda criado com sucesso',
    ]
];

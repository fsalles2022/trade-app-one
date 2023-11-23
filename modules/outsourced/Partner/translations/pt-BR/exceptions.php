<?php

use Outsourced\Partner\Exceptions\PartnerExceptions;

return [
    PartnerExceptions::UNAVAILABLE => 'Ocorreu um erro ao tentar autenticar no parceiro informado.',
    PartnerExceptions::NOT_FOUND => 'AccessKey não pertence a nenhum integrador, verifique o valor enviado e tente novamente.',
    PartnerExceptions::NOT_IMPLEMENTED => 'Partner não implementado.',
    PartnerExceptions::USER_NOT_BELONGS => 'Usuário cadastrado, porém está vinculado a uma rede ou operadora diferente da que você tem acesso.',
    PartnerExceptions::REQUEST_ERROR => 'Ocorreu um problema ao requisitar dados na API do parceiro.',
    PartnerExceptions::INVALID_TOKEN => 'Token inválido ou expirado, efetue o processo para gerar um novo token.',
    PartnerExceptions::INVALID_CREDENTIAL_URL => 'Integrador não possui uma URL de validação de credencial cadastrada. Efetue o cadastro e tente novamente.',
    PartnerExceptions::NOT_FOUND_DEFAULT_REDIRECT => 'URL padrão de redirecionamento não encontrada para o integrador informado.'
];

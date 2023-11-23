<?php

use Generali\Exceptions\GeneraliExceptions;

return [
    GeneraliExceptions::INSURANCE_TICKET_NOT_CREATED => 'Não foi possível gerar o Bilhete, entre em contato com o suporte',
    GeneraliExceptions::SERVICE_NOT_ACTIVATED        => 'Não foi possível realizar a ativação, Gateway com resposta incorreta.',
    GeneraliExceptions::INCORRECT_SERVICE_STATUS     => 'Status do serviço incorreto, o mesmo precisa estar como Não Ativado.',
    GeneraliExceptions::SERVICE_NOT_CANCELLED        => 'Serviço não cancelado, verifique o status da venda.',
    GeneraliExceptions::PRODUCT_NOT_FOUND            => 'Não há produtos disponiveis com base no valor informado, R$::value',
];

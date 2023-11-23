<?php


use Gateway\Exceptions\GatewayExceptions;

return [
    GatewayExceptions::GATEWAY_UNAVAILABLE => 'Serviço de pagamento indisponível, por favor tente mais tarde',
    GatewayExceptions::GATEWAY_TRANSACTION_NOT_APPROVED => 'Transação não aprovada, por favor, tente novamente ou utilize outro cartão',
    GatewayExceptions::GATEWAY_ERROR_CANCELING_THE_SALE => 'Ocorreu um erro ao cancelar o serviço, por favor tente novamente ou contate o suporte',
    GatewayExceptions::CARD_UNAUTHORIZED => 'Cartão não autorizado.',
    GatewayExceptions::TOKEN_CARD_INVALID => 'Ocorreu um erro ao gerar o token do cartão de crédito, por favor, tente novamente.',
    GatewayExceptions::TRANSACTION_ID_NOT_FOUND => 'Identificação do Gateway Transaction não foi encontrado.'
];

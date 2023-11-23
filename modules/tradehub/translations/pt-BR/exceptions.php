<?php

use Tradehub\Exceptions\TradeHubExceptions;

return [
    TradeHubExceptions::COULD_NOT_AUTHENTICATE => 'Não foi possível realizar a autenticação.',
    TradeHubExceptions::UNAVAILABLE_SERVICE => 'Não solicitar o token de portabilidade, tente novamente.',
    TradeHubExceptions::INVALID_CODE => 'Código de portabilidade inválido',
    TradeHubExceptions::INVALID_CAPTCHA_CODE => 'Ops! Captcha inválido',
    TradeHubExceptions::COULD_NOT_ADD_CHECKOUT_ITEM => 'Não foi possível adicionar item ao carrinho.',
    TradeHubExceptions::COULD_NOT_LIST_PAYMENT_OPTIONS => 'Não foi possível obter os meios de pagamentos.',
    TradeHubExceptions::COULD_NOT_GENERATE_ORDER => 'Não foi possível gerar o pedido.',
    TradeHubExceptions::COULD_NOT_ACTIVATE_SERVICE => 'Não foi possível ativar os serviços. :message',
    TradeHubExceptions::SALE_NOT_FOUND => 'Não foi possível encontrar a venda.',
    TradeHubExceptions::CHECKOUT_PRODUCT_ITEM_EMPTY => 'Campo checkout product item vazio.',
];

<?php

use Authorization\Exceptions\OriginNotFoundInWhiteListException;
use Authorization\Exceptions\RouteNotAvailableException;

return [
    OriginNotFoundInWhiteListException::KEY                       => [
        'message' => 'Domínio/IP da Requisição não encontrada na lista de acesso.',
        'description' => 'Contate o desenvolvimento da Trade Up para verificar acesso.'
    ],
    RouteNotAvailableException::KEY => [
        'message' => 'Rota não liberada para chave informada.',
        'description' => 'Contate o desenvolvimento da Trade Up para verificar acesso.'
    ]
];

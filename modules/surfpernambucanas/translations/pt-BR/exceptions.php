<?php

use SurfPernambucanas\Exceptions\PagtelExceptions;

return [
    'pagtel' => [
        PagtelExceptions::NOT_AUTHENTICATED  => 'Não foi possível se conectar ao parceiro.',
        PagtelExceptions::PLAN_NOT_FOUND     => 'Não foi possível encontrar o plano.',
        PagtelExceptions::FAIL_REQUEST       => 'Operadora informa: :message.',
    ],
];

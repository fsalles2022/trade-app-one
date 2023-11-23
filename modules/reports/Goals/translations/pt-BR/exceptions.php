<?php

use Reports\Goals\Exceptions\GoalsExceptions;

return [
    'goal' => [
        'invalid_date' => 'Verifique o periodo da meta, deve ser do ano atual e o mes entre 1 e 12',
        'invalid_type' => 'Tipo de meta inexistente: :type',
        'invalid_goal' => 'Valor de meta invalido: :type',
        'pdv_not_authorized' => 'Voce nao possui permissao sobre este ponto de venda',
        GoalsExceptions::MONTH_GOALS_NOT_FOUND => 'Nao foram encontradas metas com os parametros informados'
    ]
];

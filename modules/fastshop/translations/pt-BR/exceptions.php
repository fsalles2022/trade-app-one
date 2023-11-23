<?php

use \FastShop\Exceptions\FastshopExceptions;

return [
    'credentials' => [
        'invalid' => 'Ocorreu um eror ao tentar autenticar na api do parceiro FastShop'
    ],
    FastshopExceptions::GENERAL_API_ERROR => 'Ocorreu um erro ao requisitar dados na Api do parceiro.',
    FastshopExceptions::SIMULATE_EMPTY_DEVICE_PRICE => 'Ao tentar realizar a simulação o parceiro obteve um erro ao buscar o dispositivo.'
];

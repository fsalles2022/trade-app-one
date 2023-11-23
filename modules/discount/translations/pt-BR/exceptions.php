<?php

use Discount\Exceptions\DiscountExceptions;
use Discount\Exceptions\ImeiExceptions;

return [
    DiscountExceptions::NOT_FOUND => 'Triangulação não encontrada.',
    DiscountExceptions::DEVICE_ALREADY_DISCOUNT =>
        'O dispositivo :device já está cadastrado em uma triangulação para algumas das lojas selecionadas no período selecionado.',
    DiscountExceptions::HAS_NOT_AUTHORIZATION => 'Usuário não possui permissão sobre esta Triangulation',
    DiscountExceptions::FAIL_FETCHING_TRIANGULATION => 'Houve uma falha inesperada ao recuperar as triangulações',
    DiscountExceptions::ERROR_CHANGING_DATE => 'Erro na mudança de data para a triangulação :discount pelo seguinte motivo: ',
    ImeiExceptions::UNAUTHORIZED => 'Não autorizado para realizar a operação'
];

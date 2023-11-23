<?php

use Core\HandBooks\Exceptions\HandbookExceptions;

return [
    HandbookExceptions::NOT_FOUND => 'Manual não encontrado.',
    HandbookExceptions::OPERATION_NOT_FOUND => 'Módulo não encontrado.',
    HandbookExceptions::TYPE_INVALID => 'Tipo de manual inválido.',
    HandbookExceptions::HAS_NOT_PERMISSION_UNDER_HANDBOOK => 'Usuário não possui permissões sobre este manual.'
];

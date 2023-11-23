<?php

use TradeAppOne\Domain\Enumerators\Operations;

return [
    'ACCEPTED'              => 'EM ANALISE',
    'PENDING_SUBMISSION'    => 'NAO ATIVADO',
    'SUBMITTED'             => 'FALHA/NAO ATIVADO',
    'APPROVED'              => 'ATIVADO',
    'REJECTED'              => 'REJEITADO',
    'CANCELED'              => 'CANCELADO',
    'MIGRATION'             => 'MIGRACAO',
    'ACTIVATION'            => 'ATIVACAO',
    'PORTABILITY'           => 'PORTABILIDADE',
    'VAREJO'                => 'VAREJO',
    'WEB'                   => 'WEB',
    'TELECOMMUNICATION'     => 'TELEFONIA',
    Operations::MOBILE_APPS => 'TELEFONIA'
];

<?php

namespace TradeAppOne\Exports\Operators;

use Illuminate\Foundation\Bus\PendingDispatch;
use TradeAppOne\Mail\MailRegistrations;

class RegisterMailToNextel implements RegisterMailToOperator
{
    private const COLUMNS = [
        'Código PDV Nextel',
        'Código FTE Nextel',
        'Nome do vendedor',
        'CPF',
        'REDE',
        'Codigo Loja',
        'UF'
    ];

    public const EMAILS_NEXTEL    = ['patricia.santos@nextel.com.br'];
    public const EMAILS_NEXTEL_CC = [
        'mauricio.almeida@nextel.com.br',
        'natalia.costa2@nextel.com.br',
        'marco.Silva@nextel.com.br'
    ];

    private function getSql(string $network): string
    {
        $queryString = file_get_contents(__DIR__ . '/Queries/UsersQueryForNextel.sql');
        return str_replace('$networks', "'$network'", $queryString);
    }

    public function build(string $networks): PendingDispatch
    {
        return dispatch(new MailRegistrations(
            $this->getSql($networks),
            self::COLUMNS,
            self::EMAILS_NEXTEL,
            self::EMAILS_NEXTEL_CC
        ));
    }
}

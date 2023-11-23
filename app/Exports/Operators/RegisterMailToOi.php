<?php

namespace TradeAppOne\Exports\Operators;

use Illuminate\Foundation\Bus\PendingDispatch;
use TradeAppOne\Mail\MailRegistrations;

class RegisterMailToOi implements RegisterMailToOperator
{
    public $emailsOi;
    public $emailsOiCC;

    public const COLUMNS = [
        'REDE',
        'SAP_PDV_OI',
        'NOME PROMOTOR',
        'CPF',
        'DDD',
        'TELEFONE',
        'DT ADMISSÃO',
        'DT DEMISSÃO',
        'STATUS',
        'CANAL',
        'REGIONAL',
        'TIPO',
        'RANGE'
    ];

    public function __construct()
    {
        $this->emailsOi   = $this->sanitizedMails(config('utils.senderMails.OI.emails'));
        $this->emailsOiCC = $this->sanitizedMails(config('utils.senderMails.OI.emailsCC'));
    }

    private function getSql(string $network): string
    {
        $queryString = file_get_contents(__DIR__ . '/Queries/UsersQueryForOi.sql');
        return str_replace('$networks', "'$network'", $queryString);
    }

    public function build(string $networks): ?PendingDispatch
    {
        if (count($this->emailsOi) === 0 || count($this->emailsOiCC) === 0) {
            return null;
        }

        return dispatch(new MailRegistrations(
            $this->getSql($networks),
            self::COLUMNS,
            $this->emailsOi,
            $this->emailsOiCC
        ));
    }

    /**
     * @param string $envConfig
     * @return string[]
     */
    private function sanitizedMails(string $envConfig = ''): array
    {
        if (empty($envConfig)) {
            return [];
        }

        return explode(';', $envConfig);
    }
}

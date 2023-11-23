<?php

namespace Uol\Tests\ServerTest\Passaporte;

use Uol\Tests\ServerTest\UolServerMethodInterface;

class ConfirmarPassaporte extends \StdClass implements UolServerMethodInterface
{
    private $result;

    public function __construct(bool $status = true)
    {
        $status                                       = $status ? 'true' : 'false';
        $this->result                                 = new \stdClass();
        $this->result->ConfirmarPassaporteResult      = new \stdClass();
        $this->result->ConfirmarPassaporteResult->any = '<passaporte>
            <serie>16024289</serie>
            <numero><![CDATA[1193924583373377]]></numero>
            <retorno>' . $status . '</retorno>
            <mensagem><![CDATA[Passaporte Confirmado Com Sucesso!]]></mensagem>
        </passaporte>';
    }

    public function getResult(): \StdClass
    {
        return $this->result;
    }
}

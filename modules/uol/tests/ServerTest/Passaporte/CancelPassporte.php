<?php

namespace Uol\Tests\ServerTest\Passaporte;

use Uol\Tests\ServerTest\UolServerMethodInterface;

class CancelPassporte extends \StdClass implements UolServerMethodInterface
{
    private $result;

    public function __construct(bool $status = true)
    {
        $status                                      = $status ? 'true' : 'false';
        $this->result                                = new \stdClass();
        $this->result->CancelarPassaporteResult      = new \stdClass();
        $this->result->CancelarPassaporteResult->any =
            '<passaporte xmlns="">
                <serie>16054753</serie>
                <retorno>'.$status.'</retorno>
                <mensagem><![CDATA[Passaporte Cancelado Com Sucesso]]></mensagem>
            </passaporte>';
    }

    public function getResult(): \StdClass
    {
        return $this->result;
    }
}

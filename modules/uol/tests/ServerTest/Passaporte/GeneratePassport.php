<?php

namespace Uol\Tests\ServerTest\Passaporte;

use Uol\Tests\ServerTest\UolServerMethodInterface;

class GeneratePassport extends \StdClass implements UolServerMethodInterface
{
    public function __construct(bool $status = true)
    {
        $this->result                             = new \stdClass();
        $this->result->GerarPassaporteResult      = new \stdClass();
        $this->result->GerarPassaporteResult->any = $this->responseAny($status);
    }

    public function getResult(): \StdClass
    {
        return $this->result;
    }

    private function responseAny($status)
    {
        $status = ($status) ? 'true' : 'false';

        return '<passaporte xmlns="">
                    <serie>16049715</serie>
                    <numero><![CDATA[9334998045031144]]></numero>
                    <retorno>'. $status . '</retorno>
                    <mensagem><![CDATA[Passaporte Gerado com sucesso!]]></mensagem>
                </passaporte>';
    }
}

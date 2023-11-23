<?php

namespace Uol\Tests\ServerTest;

use SoapClient;
use Uol\Connection\Curso\UolCursoSoapClient;
use Uol\Tests\ServerTest\Curso\ConsultarCategorias;
use Uol\Tests\ServerTest\Curso\ConsultarCursosResgate;

class UolCursoServerMock
{
    private $soapClient;

    public function __construct()
    {
        $this->soapClient = \Mockery::mock(SoapClient::class)->makePartial();

        $this->soapClient->shouldReceive(UolCursoSoapClient::CONSULTAR_CATEGORIAS)
            ->andReturn((new ConsultarCategorias())->getResult());

        $this->soapClient->shouldReceive(UolCursoSoapClient::CONSULTAR_CURSOS_RESGATE)
            ->andReturnUsing(function ($xml) {
                $xmlParameters     = simplexml_load_string(data_get($xml, 'xmlParametros'));
                $parametersToArray = json_decode(json_encode($xmlParameters), true);
                $categoryId        = data_get($parametersToArray, 'id_categoria');
                return (new ConsultarCursosResgate($categoryId))->getResult();
            })->getMock();
    }

    public function getSoapClient(): SoapClient
    {
        return $this->soapClient;
    }
}

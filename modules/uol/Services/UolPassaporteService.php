<?php

namespace Uol\Services;

use Uol\Connection\Passaporte\UolPassaporteSoapClient;
use Uol\Exceptions\UolExceptions;
use Uol\Models\UolPassport;

class UolPassaporteService
{
    private $uolPassaporteSoapClient;

    public function __construct(UolPassaporteSoapClient $uolPassaporteSoapClient)
    {
        $this->uolPassaporteSoapClient = $uolPassaporteSoapClient;
    }

    public function generate(int $type): UolPassport
    {
        $response = $this->uolPassaporteSoapClient->passportGenerated($type);

        $status = array_get($response, 'retorno');
        $numero = array_get($response, 'numero');
        $serie  = array_get($response, 'serie');

        if ($status == 'true') {
            $uolPassport = new UolPassport($serie, $numero);
            return $this->confirmation($uolPassport);
        }

        return new UolPassport();
    }

    public function confirmation(UolPassport $passport): UolPassport
    {
        $response = $this->uolPassaporteSoapClient->confirmPassportGenerated($passport->id);
        $status   = array_get($response, 'retorno');

        if ($status == 'true') {
            return $passport->setStatus(true);
        }

        return $passport;
    }

    public function cancel(UolPassport $uolPassport): UolPassport
    {
        $response = $this->uolPassaporteSoapClient->cancelPassport($uolPassport->id);
        $status   = data_get($response, 'retorno');

        if ($status == 'true') {
            return $uolPassport->setCancel();
        }

        $message = data_get($response, 'mensagem');
        throw UolExceptions::errorCancelPassport($message);
    }
}

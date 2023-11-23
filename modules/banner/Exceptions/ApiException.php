<?php

namespace Banner\Exceptions;

abstract class ApiException extends \Exception
{
    protected $transportedMessage = '';

    public function render()
    {
        return response(['error' => $this->getError()], $this->getHttpStatus());
    }

    public function getError()
    {
        return [
            'message'            => $this->getMessage(),
            'transportedMessage' => $this->getTransportedMessage(),
            'shortMessage'       => $this->getCode(),
        ];
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    abstract public function getHttpStatus();
}

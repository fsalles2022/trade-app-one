<?php


namespace TradeAppOne\Domain\Components\RestClient;

use \Symfony\Component\HttpFoundation;

abstract class ResponseAbstract implements Response
{
    protected $statusCode;

    public function isOk()
    {
        return $this->statusCode == HttpFoundation\Response::HTTP_OK;
    }

    public function isNotFound()
    {
        return $this->statusCode == HttpFoundation\Response::HTTP_NOT_FOUND;
    }

    public function isTooManyRequests()
    {
        return $this->statusCode == HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS;
    }
}

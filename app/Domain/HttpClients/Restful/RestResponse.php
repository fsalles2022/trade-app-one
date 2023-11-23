<?php


namespace TradeAppOne\Domain\HttpClients\Restful;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use TradeAppOne\Domain\HttpClients\Responseable;

class RestResponse implements Responseable
{
    protected $body;
    protected $content;
    protected $statusCode = \Illuminate\Http\Response::HTTP_MISDIRECTED_REQUEST;
    protected $success;

    public static function success(Response $response)
    {
        $wrapper             = new RestResponse();
        $wrapper->success    = ($response->getStatusCode() === HttpResponse::HTTP_OK);
        $wrapper->body       = $response->getBody();
        $wrapper->content    = $response->getBody()->__toString();
        $wrapper->statusCode = $response->getStatusCode();

        return $wrapper;
    }

    public static function failure(BadResponseException $exception)
    {
        $wrapper = new RestResponse();

        $wrapper->success    = false;
        $wrapper->body       = $exception->getResponse()
            ? $exception->getResponse()->getBody()
            : null;
        $wrapper->content    = is_null($wrapper->body)
            ? ''
            : $wrapper->body->__toString();
        $wrapper->statusCode = $exception->getResponse()
            ? $exception->getResponse()->getStatusCode()
            : $wrapper->statusCode;

        return $wrapper;
    }

    public function toJson()
    {
        return $this->content;
    }

    public function getStatus()
    {
        return $this->statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function __toString()
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $response = json_decode($this->content, true);
        if ($response && is_array($response)) {
            return $response;
        } else {
            return [];
        }
    }

    public function get(string $key = null, $default = null)
    {
        return $key
            ? data_get($this->toArray(), $key, $default)
            : $this->content;
    }
}

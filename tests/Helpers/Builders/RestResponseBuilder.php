<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use GuzzleHttp\Exception\BadResponseException;
use function GuzzleHttp\Psr7\stream_for;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

class RestResponseBuilder
{
    private $body = '';
    private $status = 200;
    private $headers = ['Content­Type' => 'application/json'];

    public function withHeaders(array $headers): RestResponseBuilder
    {
        $this->headers = $headers;
        return $this;
    }

    public function withStatus(int $status): RestResponseBuilder
    {
        $this->status = $status;
        return $this;
    }

    public function withBody(string $body): RestResponseBuilder
    {
        $this->body = $body;
        return $this;
    }

    public function withBodyFromArray(array $body): RestResponseBuilder
    {
        $this->body = json_encode($body);
        return $this;
    }

    public function withBodyFromFile(string $path): RestResponseBuilder
    {
        $this->body = file_get_contents($path);
        return $this;
    }

    public function success()
    {
        return RestResponse::success($this->getResponse());
    }

    private function getResponse(): Response
    {
        return new Response($this->status, $this->headers, stream_for($this->body));
    }

    public function failure()
    {
        $badResponse = new BadResponseException('', new Request('', ''), $this->getResponse());

        return RestResponse::failure($badResponse);
    }
}
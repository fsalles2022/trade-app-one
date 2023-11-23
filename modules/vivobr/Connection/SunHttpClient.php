<?php

namespace VivoBR\Connection;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Http\UploadedFile;
use TradeAppOne\Domain\Components\RestClient\Rest;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use VivoBR\Connection\Headers\SunHeader;
use VivoBR\Exceptions\SunUnavailableException;
use VivoBR\Helpers\SunHelper;

class SunHttpClient implements Rest
{
    private $client;
    private $baseUrl;
    private $requestKey;
    private $sunHeaders;

    public function __construct(Rest $client, SunHeader $sunHeaders)
    {
        $this->client     = $client;
        $this->sunHeaders = $sunHeaders;
        $this->baseUrl    = $sunHeaders->getUri();
    }

    public function selectConnection(SunHeader $header)
    {
        $this->client->addHeaders($header->getHeaders());
    }

    public function post(string $url): Rest
    {
        $this->client->post($this->baseUrl . $url);
        return $this;
    }

    public function get(string $url): Rest
    {
        $this->client->get($this->baseUrl . $url);
        return $this;
    }

    public function put(string $url): Rest
    {
        $this->client->put($this->baseUrl . $url);
        return $this;
    }

    public function delete(string $url): Rest
    {
        $this->client->delete($this->baseUrl . $url);
        return $this;
    }

    public function addHeaders(array $data = []): Rest
    {
        $this->client->addHeaders(array_merge($data, $this->getDefaultHeaders()));
        return $this;
    }

    private function getDefaultHeaders()
    {
        return $this->sunHeaders->getHeaders();
    }

    public function withData(array $data): Rest
    {
        $this->client->withData(SunHelper::cryptParams($data, env('SUN_API_TOKEN')));
        return $this;
    }

    public function withQuery(array $query): Rest
    {
        $this->client->withQuery(SunHelper::cryptParams($query, env('SUN_API_TOKEN')));
        return $this;
    }

    public function addFile(UploadedFile $file): Rest
    {
        $this->client->addFile($file);

        return $this;
    }

    public function execute(): RestResponse
    {
        $start = microtime(true);
        try {
            $response = RestResponse::success($this->client->execute());
            heimdallLog()->realm(Operations::VIVO)
                ->start($start)
                ->end(microtime(true))
                ->request($this->client->options)
                ->response($response)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ClientException $exception) {
            heimdallLog()
                ->start($start)
                ->end(microtime(true))
                ->realm(Operations::VIVO)
                ->request($this->client->options)
                ->response($exception->getResponse()->getBody()->__toString())
                ->fire();
            return RestResponse::failure($exception);
        } catch (ConnectException | ServerException $exception) {
            heimdallLog()
                ->start($start)
                ->end(microtime(true))
                ->realm(Operations::VIVO)
                ->request($this->client->options)
                ->response($exception->getResponse())
                ->fire();
            $message = $exception->getResponse() === null
                ? $exception->getMessage()
                : $exception->getResponse()->getBody()->__toString();
            throw new SunUnavailableException($message);
        }
    }
}

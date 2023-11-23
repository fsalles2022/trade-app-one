<?php

namespace OiBR\Connection\ElDoradoGateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use OiBR\Exceptions\OiBRElDoradoUnavailable;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class ElDoradoHttpClient extends RestFulClient
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        try {
            return $this->client->request($method, $url, $options);
        } catch (ServerException |ClientException $exception) {
            $response = $exception->getResponse();
            Log::alert('eldorado-down', [
                'message'  => $exception->getMessage(),
                'response' => $response->getBody()->__toString()
            ]);
            return $response;
        } catch (ConnectException $exception) {
            Log::alert('eldorado-down', ['message' => $exception->getMessage()]);
            throw new OiBRElDoradoUnavailable();
        }
    }
}

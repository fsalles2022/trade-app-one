<?php

namespace Core\WebHook\Connections\Clients;

use Core\WebHook\Connections\Logs\WebHookLogConnection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use RuntimeException;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class WebHookHttpClient extends RestFulClient
{
    protected $client;
    protected $logger;

    public function __construct(Client $client, WebHookLogConnection $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param array $body
     * @param string $destiny
     * @throws Exception
     */
    public function send(array $body, string $destiny): void
    {
        $response = $this->request($body, $destiny);

        if ($response->isSuccess()) {
            return;
        }

        throw new RuntimeException($response->get());
    }

    /**
     * @param array $body
     * @param string $destiny
     * @return Responseable
     * @throws Exception
     */
    private function request(array $body, string $destiny): Responseable
    {
        $configs = $this->getConfig($destiny);

        $method  = data_get($configs, 'method', '');
        $url     = data_get($configs, 'url', '');
        $headers = data_get($configs, 'headers', '');

        try {
            $response = $this->$method($url, $body, $headers);

            $this->logger
                ->request($body)
                ->destiny($destiny)
                ->configs($configs)
                ->response($response)
                ->save();

            return $response;
        } catch (Exception $exception) {
            $this->logger
                ->request($body)
                ->destiny($destiny)
                ->configs($configs)
                ->exception($exception)
                ->save();

            throw $exception;
        }
    }

    public function getConfig(string $destiny): array
    {
        return config("webhookClients.$destiny", []);
    }

    public function execute($method, $url, $options): Response
    {
        return $this->client->request($method, $url, $options);
    }
}

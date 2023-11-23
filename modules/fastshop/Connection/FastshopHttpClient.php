<?php

namespace FastShop\Connection;

use FastShop\Exceptions\FastshopUnavailableException;
use Illuminate\Support\Facades\Cache;
use FastShop\Exceptions\FastshopInvalidCredentialsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class FastshopHttpClient extends RestFulClient
{
    private const CACHE_FASTSHOP_BEARER = 'CACHE_FASTSHOP_BEARER';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function authenticate(): void
    {
        $bearer = Cache::get(self::CACHE_FASTSHOP_BEARER);

        if (! $bearer) {
            $fastShopHeaders = app()->make(FastshopHeaders::class);
            $url             = FastshopRoutes::AUTH . '?grant_type=' . $fastShopHeaders->getGrantType();

            $response = $this->postFormParams($url, [
                'client_id' => $fastShopHeaders->getClientId(),
                'client_secret' => $fastShopHeaders->getClientSecret()
            ]);

            $responseArray = $response->toArray();
            $bearer        = data_get($responseArray, 'access_token');

            throw_if(
                ! $response->isSuccess() || blank($bearer),
                new FastshopInvalidCredentialsException(data_get($responseArray, 'Error'))
            );

            Cache::put(self::CACHE_FASTSHOP_BEARER, $bearer, 50);
        }

        $this->pushHeader(['Authorization' => 'Bearer ' . $bearer]);
    }

    /**
     * @param $method
     * @param $url
     * @param $options
     * @return Response|ResponseInterface
     * @throws GuzzleException
     * @throws FastshopUnavailableException
     */
    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(NetworkEnum::FAST_SHOP)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (RequestException $exception) {
            heimdallLog()->realm(NetworkEnum::FAST_SHOP)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->catchException($exception)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            throw new FastshopUnavailableException($exception->getMessage());
        }
    }
}

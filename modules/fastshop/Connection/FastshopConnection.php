<?php

namespace FastShop\Connection;

use FastShop\Exceptions\FastshopExceptions;
use FastShop\Exceptions\FastshopInvalidCredentialsException;
use Illuminate\Contracts\Encryption\DecryptException;
use TradeAppOne\Domain\HttpClients\Responseable;

class FastshopConnection implements FastshopConnectionInterface
{
    protected $fastshopClient;

    public function __construct(FastshopHttpClient $fastshopHttpClient)
    {
        $this->fastshopClient = $fastshopHttpClient;
    }

    /** @throws */
    public function products(array $filters = []): Responseable
    {
        $this->authenticate();
        $response = $this->fastshopClient->get(FastshopRoutes::LIST_PRODUCTS, $filters);

        throw_if(
            $response->isSuccess() && $response->get('message') !== null,
            FastshopExceptions::GeneralApiError($response->get('message'))
        );

        throw_if(
            ! $response->isSuccess(),
            FastshopExceptions::GeneralApiError($response->get('error.message'))
        );

        return $response;
    }

    /** @throws */
    public function productPrice(array $params = []): Responseable
    {
        $this->authenticate();

        $storeId = data_get($params, 'pos');
        $sku     = data_get($params, 'device');

        $response = $this->fastshopClient->get(FastshopRoutes::DEVICE_PRICE, [
            'id_loja' => $storeId,
            'sku' => $sku,
        ]);

        throw_if(
            $response->isSuccess() && $response->get('message') !== null,
            FastshopExceptions::GeneralApiError($response->get('message'))
        );

        return $response;
    }

    /** @throws */
    private function authenticate(): void
    {
        try {
            $this->fastshopClient->authenticate();
        } catch (DecryptException $exception) {
            throw new FastshopInvalidCredentialsException();
        }
    }
}

<?php

declare(strict_types=1);

namespace SurfPernambucanas\Connection;

use Illuminate\Support\Facades\Cache;
use SurfPernambucanas\Exceptions\PagtelExceptions;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\BuildExceptions;

class PagtelConnection
{
    public const AUTHENTICATE_PAYLOAD_CACHE_NAME = 'AUTHENTICATE_DATA_PAGTEL_API';
    public const AUTHENTICATE_EXPIRES_IN         = 50;
    public const PAGTEL_PERNAMBUCANAS            = 'pernambucanas';
    public const PAGTEL_CORREIOS                 = 'correios-pernambucanas';

    /** @var PagtelHttpClient */
    protected $httpClient;

    /** @var PagtelAuthUser */
    protected $userAuth;

    public function __construct(PagtelHttpClient $httpClient, PagtelAuthUser $userAuth)
    {
        $this->httpClient = $httpClient;
        $this->userAuth   = $userAuth;
    }

    /** @throws BuildExceptions */
    public function authenticate(): Responseable
    {
        $response = $this->httpClient->post(PagtelRoutes::AUTHENTICATE, $this->getCredentials(), [], false);

        if (((bool) $response->get('authenticated')) === false) {
            throw PagtelExceptions::notAuthenticated();
        }

        return $response;
    }

    public function getToken(): string
    {
        $data = Cache::remember(
            self::AUTHENTICATE_PAYLOAD_CACHE_NAME . $this->userAuth->getIdentify(),
            self::AUTHENTICATE_EXPIRES_IN,
            function (): array {
                return $this->authenticate()->toArray();
            }
        );

        return $data['accessToken'] ?? '';
    }

    public function pushTokenHeader(): void
    {
        $this->httpClient->pushHeader([
            'Authorization' => 'Bearer '. $this->getToken(),
        ]);
    }

    /** @param string[] $payload */
    public function subscriberActivate(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::SUBSCRIBER_ACTIVATE, $payload);
    }

    /** @param string[] $payload */
    public function allocateMsisdn(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::ALLOCATED_MSISDN, $payload);
    }

    /** @param string[] $payload */
    public function plans(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::GET_VALUES, $payload);
    }

    /** @param string[] $payload */
    public function getCards(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::GET_CARD, $payload);
    }

    /** @param string[] $payload */
    public function addCard(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::ADD_CARD, $payload);
    }

    /** @param string[] $payload */
    public function recharge(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::RECHARGE, $payload);
    }

    /** @param string[] $payload */
    public function submitPortin(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::SUBMIT_PORTIN, $payload);
    }

    public function activationPlans(): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->get(PagtelRoutes::PLANS);
    }

    /** @param string[] $payload */
    public function activationActivate(array $payload): Responseable
    {
        $this->pushTokenHeader();

        return $this->httpClient->post(PagtelRoutes::ACTIVATIONS, $payload);
    }

    /** @return string[] */
    protected function getCredentials(): array
    {
        return [
            'login'     => $this->userAuth->getLogin(),
            'senha'     => $this->userAuth->getPassword(),
            'grantType' => $this->userAuth->getGrantType(),
        ];
    }
}

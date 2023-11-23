<?php

declare(strict_types=1);

namespace Tradehub\Connection;

use Illuminate\Support\Facades\Cache;
use Throwable;
use TradeAppOne\Domain\HttpClients\Responseable;
use Tradehub\Adapters\TradeHubGetSellerTokenPartner;
use Tradehub\Adapters\TradeHubPayloadAdapter;
use Tradehub\Enumerators\TradeHubCaches;
use Tradehub\Exceptions\TradeHubExceptions;
use Tradehub\TradeHubHeaders;

class TradeHubConnection
{
    /**
     * @var TradeHubHttpClient
     */
    private $tradeHubHttpClient;
    /**
     * @var TradeHubHeaders
     */
    private $tradeHubHeaders;

    public function __construct(TradeHubHttpClient $tradeHubHttpClient, TradeHubHeaders $tradeHubHeaders)
    {
        $this->tradeHubHttpClient = $tradeHubHttpClient;
        $this->tradeHubHeaders    = $tradeHubHeaders;
    }

    /**
     * @throws Throwable
     */
    public function authenticate(): Responseable
    {
        $response = $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_AUTHENTICATE, $this->tradeHubHeaders->getCredentials(), []);
        throw_unless($response->get('success'), TradeHubExceptions::couldNotAuthenticate());

        return $response;
    }

    /**
     * @throws Throwable
     */
    public function authenticateSeller(string $token): Responseable
    {
        $this->pushTokenHeaderSeller($token);
        $response = $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_AUTHENTICATE_SELLER, $this->tradeHubHeaders->getCredentialsSeller(), []);
        throw_unless($response->get('success'), TradeHubExceptions::couldNotAuthenticate());

        return $response;
    }

    /**
     * @throws Throwable
     */
    public function authenticateSellerByViaVarejo(string $token, string $cpf): Responseable
    {
        $this->pushTokenHeaderSeller($token);

        $body = [
            'login' => $cpf,
            'password' => $this->tradeHubHeaders->getViaVarejoSellerPassword(),
            'partner' => 'viavarejo'
        ];

        $response = $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_AUTHENTICATE_SELLER, $body, []);
        throw_unless($response->get('success'), TradeHubExceptions::couldNotAuthenticate());

        return $response;
    }

    /**
     * @throws Throwable
     */
    public function getToken(): string
    {
        $data = Cache::remember(
            TradeHubCaches::AUTHENTICATE_BEARER,
            TradeHubCaches::UTHENTICATION_DUE,
            function (): array {
                return $this->authenticate()->toArray();
            }
        );

        return $data['response']['auth']['token'] ?? '';
    }

    /**
     * @param string $token
     * @return string
     * @throws Throwable
     */
    public function getTokenSeller(string $token): string
    {
        $data = Cache::remember(
            TradeHubCaches::AUTHENTICATE_BEARER_SELLER,
            TradeHubCaches::UTHENTICATION_DUE,
            function () use ($token): array {
                return $this->authenticateSeller($token)->toArray();
            }
        );

        return $data['response']['saleOfServicesAuth']['token'] ?? '';
    }

    /**
     * @param TradeHubGetSellerTokenPartner $tradeHubGetSellerTokenPartner
     * @return Responseable
     * @throws Throwable
     */
    public function getSellerTokenFromViaVarejo(TradeHubGetSellerTokenPartner $tradeHubGetSellerTokenPartner): Responseable
    {
        $this->pushTokenHeaderIntegration();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_GET_TOKEN_SELLER_PARTNER, $tradeHubGetSellerTokenPartner->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function sendVerificationToken(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_SEND_TOKEN_PORTABILITY, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function sendVerificationTokenTim(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_SEND_TOKEN_PORTABILITY_TIM, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function checkVerificationToken(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_VALIDATE_TOKEN_PORTABILITY, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function validateCaptcha(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        return $this->tradeHubHttpClient->post(
            TradeHubRoutes::ENDPOINT_VALIDATE_CAPTCHA_CODE,
            $tradeHubPayloadAdapter->jsonSerialize(),
            [
                'x-api-key' => $this->tradeHubHeaders->getCaptchaKey()
            ]
        );
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function checkoutItemAdd(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_CHECKOUT_ITEM_ADD, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function checkoutPaymentOptions(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_CHECKOUT_PAYMENT_OPTIONS, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function checkoutOrder(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_CHECKOUT_ORDER, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @param TradeHubPayloadAdapter $tradeHubPayloadAdapter
     * @return Responseable
     * @throws Throwable
     */
    public function checkoutActivateService(TradeHubPayloadAdapter $tradeHubPayloadAdapter): Responseable
    {
        $this->pushTokenHeader();
        return $this->tradeHubHttpClient->post(TradeHubRoutes::ENDPOINT_CHECKOUT_ACTIVATE_SERVICE, $tradeHubPayloadAdapter->jsonSerialize());
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function pushTokenHeaderIntegration(): void
    {
        $this->tradeHubHttpClient->pushHeader([
            'Authorization' => $this->getToken()
        ]);
    }

    /**
     * @return void
     * @throws Throwable
     */
    private function pushTokenHeader(): void
    {
        $token = $this->getToken();

        $this->tradeHubHttpClient->pushHeader([
            'Authorization' => $token,
            'AuthorizationSaleOfServices' => $this->getTokenSeller($token)
        ]);
    }

    /**
     * @param string $token
     * @return void
     */
    private function pushTokenHeaderSeller(string $token): void
    {
        $this->tradeHubHttpClient->pushHeader([
            'Authorization' => $token,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Tradehub\Services;

use Throwable;
use Tradehub\Adapters\TradeHubCheckoutItemAdd;
use Tradehub\Adapters\TradeHubCheckoutOrder;
use Tradehub\Adapters\TradeHubCheckToken;
use Tradehub\Adapters\TradeHubSaleOfServiceId;
use Tradehub\Adapters\TradeHubSendToken;
use Tradehub\Adapters\TradeHubValidateCaptcha;
use Tradehub\Connection\TradeHubConnection;
use Tradehub\Exceptions\TradeHubExceptions;

class TradeHubService
{
    /**
     * @var TradeHubConnection
     */
    private $tradeHubConnection;

    public function __construct(TradeHubConnection $tradeHubConnection)
    {
        $this->tradeHubConnection = $tradeHubConnection;
    }

    /**
     * @param array $attributes
     * @return array
     * @throws Throwable
     */
    public function sendVerificationToken(array $attributes): array
    {
        $response = $this->tradeHubConnection->sendVerificationToken(
            new TradeHubSendToken(
                $attributes['customer']['phoneNumber'] ?? null,
                $attributes['origin'] ?? null
            )
        );

        throw_unless($response->isSuccess(), (new TradeHubExceptions)->unavailableService($response->toArray()));

        return $response->toArray();
    }

    /**
     * @param array $attributes
     * @return array
     * @throws Throwable
     */
    public function sendVerificationTokenTim(array $attributes): array
    {
        $response = $this->tradeHubConnection->sendVerificationTokenTim(
            new TradeHubSendToken(
                $attributes['customer']['phoneNumber'] ?? null,
                $attributes['origin'] ?? null
            )
        );

        throw_unless($response->isSuccess(), (new TradeHubExceptions)->unavailableService($response->toArray()));

        return $response->toArray();
    }

    /**
     * @param array $attributes
     * @return array
     * @throws Throwable
     */
    public function checkTheSentToken(array $attributes): array
    {
        $response = $this->tradeHubConnection->checkVerificationToken(
            new TradeHubCheckToken(
                $attributes['phoneNumber'] ?? null,
                $attributes['code'] ?? null,
                $attributes['origin'] ?? null
            )
        );

        throw_unless($response->isSuccess(), (new TradeHubExceptions)->unavailableService($response->toArray()));

        throw_if($response->get('response.validated', false) === false, (new TradeHubExceptions)::invalidCode());

        return $response->toArray();
    }

    public function validateCaptcha(string $code, string $key): array
    {
        $response = $this->tradeHubConnection->validateCaptcha(
            new TradeHubValidateCaptcha(
                $code,
                $key
            )
        );

        throw_if($response->get('response.validate', false) === false, (new TradeHubExceptions)::invalidCaptchaCode());

        return $response->toArray();
    }


    /**
     * @return mixed[]
     * @throws Throwable
     */
    public function checkoutItemAdd(
        ?string $checkoutItemId,
        string $saleOfServiceId,
        string $productId,
        string $promotionId,
        ?string $productResidentialProductTypeId = null,
        int $productResidentialProductAmount = 0
    ): array {
        $response = $this->tradeHubConnection->checkoutItemAdd(
            new TradeHubCheckoutItemAdd(
                $checkoutItemId,
                $saleOfServiceId,
                $productId,
                $promotionId,
                $productResidentialProductTypeId,
                $productResidentialProductAmount
            )
        );

        throw_unless(
            $response->isSuccess() || $response->get('error', false) === false,
            (new TradeHubExceptions)->checkoutItemAddException()
        );

        return $response->toArray();
    }

    /**
     * @return mixed[]
     * @throws Throwable
     */
    public function checkoutPaymentOptions(string $saleOfServiceId): array
    {
        $response = $this->tradeHubConnection->checkoutPaymentOptions(
            new TradeHubSaleOfServiceId(
                $saleOfServiceId
            )
        );

        throw_unless(
            $response->isSuccess() || $response->get('error', false) === false,
            (new TradeHubExceptions)->listPaymentOptionsException()
        );

        return $response->toArray();
    }

    /**
     * @return mixed[]
     * @throws Throwable
     */
    public function checkoutOrder(string $saleOfServiceId, array $products): array
    {
        $response = $this->tradeHubConnection->checkoutOrder(
            new TradeHubCheckoutOrder(
                $saleOfServiceId,
                $products
            )
        );

        throw_unless(
            $response->isSuccess() || $response->get('response.orderGeneratedSuccessfully') === true,
            (new TradeHubExceptions)->checkoutOrderException($response->toArray())
        );

        return $response->toArray();
    }

    /**
     * @return mixed[]
     * @throws Throwable
     */
    public function checkoutActivateService(string $saleOfServiceId): array
    {
        $response = $this->tradeHubConnection->checkoutActivateService(
            new TradeHubSaleOfServiceId(
                $saleOfServiceId
            )
        );

        throw_unless(
            $response->isSuccess() || $response->get('error', false) === false,
            (new TradeHubExceptions)->checkoutActivateServiceException($response->toArray())
        );

        return $response->toArray();
    }
}

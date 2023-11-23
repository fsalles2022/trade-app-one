<?php

declare(strict_types=1);

namespace Tradehub\Exceptions;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use TradeAppOne\Exceptions\BuildExceptions;

final class TradeHubExceptions
{
    public const COULD_NOT_AUTHENTICATE         = 'couldNotAuthenticate';
    public const UNAVAILABLE_SERVICE            = 'unavailableService';
    public const INVALID_CODE                   = 'invalidCode';
    public const INVALID_CAPTCHA_CODE           = 'invalidCaptchaCode';
    public const COULD_NOT_ADD_CHECKOUT_ITEM    = 'couldNotAddCheckoutItem';
    public const COULD_NOT_LIST_PAYMENT_OPTIONS = 'couldNotListPaymentOptions';
    public const COULD_NOT_GENERATE_ORDER       = 'couldNotGenerateOrder';
    public const COULD_NOT_ACTIVATE_SERVICE     = 'couldNotActivateService';
    public const SALE_NOT_FOUND                 = 'saleNotFound';
    public const CHECKOUT_PRODUCT_ITEM_EMPTY    = 'checkoutProductItemEmpty';

    /**
     * @return BuildExceptions
     */
    public static function couldNotAuthenticate(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COULD_NOT_AUTHENTICATE,
            'message'      => trans('tradehub::exceptions.' . self::COULD_NOT_AUTHENTICATE),
            'httpCode'     => ResponseAlias::HTTP_UNAUTHORIZED
        ]);
    }

    /**
     * @param array|null $response
     * @return BuildExceptions
     */
    public function unavailableService(?array $response): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE_SERVICE,
            'message' => $response['message'] ?? trans('tradehub::exceptions.' . self::UNAVAILABLE_SERVICE),
            'description' => trans('tradehub::exceptions.' . self::UNAVAILABLE_SERVICE),
            'httpCode' => ResponseAlias::HTTP_SERVICE_UNAVAILABLE
        ]);
    }

    /**
     * @return BuildExceptions
     */
    public static function invalidCode(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_CODE,
            'message' => trans('tradehub::exceptions.' . self::INVALID_CODE),
            'httpCode' => ResponseAlias::HTTP_BAD_REQUEST
        ]);
    }

    /**
     * @return BuildExceptions
     */
    public static function invalidCaptchaCode(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INVALID_CAPTCHA_CODE,
            'message' => trans('tradehub::exceptions.' . self::INVALID_CAPTCHA_CODE),
            'httpCode' => ResponseAlias::HTTP_BAD_REQUEST
        ]);
    }

    public static function checkoutItemAddException(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COULD_NOT_ADD_CHECKOUT_ITEM,
            'message' => trans('tradehub::exceptions.' . self::COULD_NOT_ADD_CHECKOUT_ITEM),
            'httpCode' => ResponseAlias::HTTP_BAD_REQUEST
        ]);
    }

    public static function listPaymentOptionsException(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COULD_NOT_LIST_PAYMENT_OPTIONS,
            'message' => trans('tradehub::exceptions.' . self::COULD_NOT_LIST_PAYMENT_OPTIONS),
            'httpCode' => ResponseAlias::HTTP_BAD_REQUEST
        ]);
    }

    /**
     * @param array|null $response
     * @return BuildExceptions
     */
    public function checkoutOrderException(?array $response): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COULD_NOT_GENERATE_ORDER,
            'message' => $response['message'] ?? trans('tradehub::exceptions.' . self::COULD_NOT_GENERATE_ORDER),
            'description' => trans('tradehub::exceptions.' . self::COULD_NOT_GENERATE_ORDER),
            'httpCode' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    /**
     * @param array|null $response
     * @return BuildExceptions
     */
    public function checkoutActivateServiceException(?array $response): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::COULD_NOT_ACTIVATE_SERVICE,
            'message' => trans('tradehub::exceptions.' . self::COULD_NOT_ACTIVATE_SERVICE, ['message' => $response['message'] ?? '']),
            'description' => trans('tradehub::exceptions.' . self::COULD_NOT_ACTIVATE_SERVICE, ['message' => $response['message'] ?? '']),
            'httpCode' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public function saleNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SALE_NOT_FOUND,
            'message' => trans('tradehub::exceptions.' . self::SALE_NOT_FOUND),
            'description' => trans('tradehub::exceptions.' . self::SALE_NOT_FOUND),
            'httpCode' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public function checkoutProductItemEmpty(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CHECKOUT_PRODUCT_ITEM_EMPTY,
            'message' => trans('tradehub::exceptions.' . self::CHECKOUT_PRODUCT_ITEM_EMPTY),
            'description' => trans('tradehub::exceptions.' . self::CHECKOUT_PRODUCT_ITEM_EMPTY),
            'httpCode' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Tradehub\Connection;

final class TradeHubRoutes
{
    public const ENDPOINT_AUTHENTICATE               = '/api/auth';
    public const ENDPOINT_AUTHENTICATE_SELLER        = '/api/sale-of-services/auth';
    public const ENDPOINT_SEND_TOKEN_PORTABILITY     = '/api/sale-of-services/checkout/portability/send-code-authorization';
    public const ENDPOINT_SEND_TOKEN_PORTABILITY_TIM = '/api/sale-of-services/checkout/portability/send-code-authorization-tim';
    public const ENDPOINT_VALIDATE_TOKEN_PORTABILITY = '/api/sale-of-services/checkout/portability/validate-code-authorization';
    public const ENDPOINT_GET_TOKEN_SELLER_PARTNER   = '/api/sale-of-services/authPartnerViaVarejo';
    public const ENDPOINT_CHECKOUT_ITEM_ADD          = '/api/sale-of-services/checkout/item/add';
    public const ENDPOINT_CHECKOUT_PAYMENT_OPTIONS   = '/api/sale-of-services/checkout/payment-options';
    public const ENDPOINT_CHECKOUT_ORDER             = '/api/sale-of-services/checkout/order';
    public const ENDPOINT_CHECKOUT_ACTIVATE_SERVICE  = '/api/sale-of-services/checkout/activate-service';
    public const ENDPOINT_VALIDATE_CAPTCHA_CODE      = '/tools/captcha/validate';
}

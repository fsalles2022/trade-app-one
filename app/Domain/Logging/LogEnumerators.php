<?php

namespace TradeAppOne\Domain\Logging;

class LogEnumerators
{
    const CUSTOMER_ID         = 'customer';
    const CUSTOMER_NAME       = 'customer-name';
    const INTEGRATION_SUCCESS = 'success-integration';
    const INTEGRATION_FAILED  = 'failed-integration';
    const TIM                 = 'tim';
    const CLARO               = 'claro';
    const VIVO                = 'vivo';
    const OI                  = 'oi';
    const SENTINEL            = 'sentinel';
    const SENTINEL_FAILED     = 'sentinel';

    const OI_SENTINEL_SUCCESS    = self::SENTINEL . '-' . self::OI;
    const TIM_SENTINEL_SUCCESS   = self::SENTINEL . '-' . self::TIM;
    const VIVO_SENTINEL_SUCCESS  = self::SENTINEL . '-' . self::VIVO;
    const CLARO_SENTINEL_SUCCESS = self::SENTINEL . '-' . self::CLARO;

    const OI_SENTINEL_FAILED    = self::SENTINEL_FAILED . '-' . self::OI;
    const TIM_SENTINEL_FAILED   = self::SENTINEL_FAILED . '-' . self::TIM;
    const VIVO_SENTINEL_FAILED  = self::SENTINEL_FAILED . '-' . self::VIVO;
    const CLARO_SENTINEL_FAILED = self::SENTINEL_FAILED . '-' . self::CLARO;

    const OI_INTEGRATION_SUCCESS    = self::OI . '-' . self::INTEGRATION_SUCCESS;
    const TIM_INTEGRATION_SUCCESS   = self::TIM . '-' . self::INTEGRATION_SUCCESS;
    const VIVO_INTEGRATION_SUCCESS  = self::VIVO . '-' . self::INTEGRATION_SUCCESS;
    const CLARO_INTEGRATION_SUCCESS = self::CLARO . '-' . self::INTEGRATION_SUCCESS;

    const OI_INTEGRATION_FAILED    = self::OI . '-' . self::INTEGRATION_FAILED;
    const TIM_INTEGRATION_FAILED   = self::TIM . '-' . self::INTEGRATION_FAILED;
    const VIVO_INTEGRATION_FAILED  = self::VIVO . '-' . self::INTEGRATION_FAILED;
    const CLARO_INTEGRATION_FAILED = self::CLARO . '-' . self::INTEGRATION_FAILED;

    const OI_INTEGRATION_SUCCESS_TAGS    = [
        self::OI                  => self::INTEGRATION_SUCCESS,
        self::INTEGRATION_SUCCESS => self::OI
    ];
    const TIM_INTEGRATION_SUCCESS_TAGS   = [
        self::TIM                 => self::INTEGRATION_SUCCESS,
        self::INTEGRATION_SUCCESS => self::TIM
    ];
    const VIVO_INTEGRATION_SUCCESS_TAGS  = [
        self::VIVO                => self::INTEGRATION_SUCCESS,
        self::INTEGRATION_SUCCESS => self::VIVO
    ];
    const CLARO_INTEGRATION_SUCCESS_TAGS = [
        self::CLARO               => self::INTEGRATION_SUCCESS,
        self::INTEGRATION_SUCCESS => self::CLARO
    ];


    const OI_INTEGRATION_FAILED_TAGS    = [
        self::OI                 => self::INTEGRATION_FAILED,
        self::INTEGRATION_FAILED => self::OI
    ];
    const TIM_INTEGRATION_FAILED_TAGS   = [
        self::TIM                => self::INTEGRATION_FAILED,
        self::INTEGRATION_FAILED => self::TIM
    ];
    const VIVO_INTEGRATION_FAILED_TAGS  = [
        self::VIVO               => self::INTEGRATION_FAILED,
        self::INTEGRATION_FAILED => self::VIVO
    ];
    const CLARO_INTEGRATION_FAILED_TAGS = [
        self::CLARO              => self::INTEGRATION_FAILED,
        self::INTEGRATION_FAILED => self::CLARO
    ];

    const OI_SENTINEL_TAGS    = [self::OI => self::SENTINEL, self::SENTINEL => self::OI];
    const TIM_SENTINEL_TAGS   = [self::TIM => self::SENTINEL, self::SENTINEL => self::TIM];
    const VIVO_SENTINEL_TAGS  = [self::VIVO => self::SENTINEL, self::SENTINEL => self::VIVO];
    const CLARO_SENTINEL_TAGS = [self::CLARO => self::SENTINEL, self::SENTINEL => self::CLARO];
}

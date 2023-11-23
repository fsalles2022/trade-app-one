<?php

declare(strict_types=1);

namespace ClaroBR\Connection;

final class Siv3Routes
{
    public const ENDPOINT_AUTHENTICATE                = '/api/auth';
    public const ENDPOINT_CHECK_EXTERNAL_SALE         = '/api/activations/claro/external-sale/pre-pago/check-exists';
    public const ENDPOINT_CREATE_EXTERNAL_SALE        = '/api/activations/claro/external-sale/pre-pago/save';
    public const ENDPOINT_EXPORT_EXTERNAL_SALE        = '/api/activations/claro/external-sale/pre-pago/export';
    public const ADDRESS_BY_POSTAL_CODE               = '/api/activations/claro/residential/address/address';
    public const TECHNICAL_VIABILITY                  = '/api/activations/claro/residential/technical-viability/hps';
    public const RESIDENTIAL_CREDIT_ANALYSIS          = '/api/activations/claro/residential/credit-analysis';
    public const ENDPOINT_RESIDENTIAL_PROPOSAL_CREATE = '/api/activations/claro/residential/proposal/create';
    public const ENDPOINT_ADDRESS                     = '/api/address/address';
    public const ENDPOINT_SEND_AUTHORIZATION          = '/api/mobile-telephony/portability/send-authorization';
    public const ENDPOINT_CHECK_AUTHORIZATION         = '/api/mobile-telephony/portability/validate-authorization';
}

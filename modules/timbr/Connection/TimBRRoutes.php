<?php

namespace TimBR\Connection;

final class TimBRRoutes
{
    public const ENCRYPT_CPF                = '/b2b/vendorManagement/v1/encripty/';
    public const CEP                        = '/b2b/geographicAddressManagement/v1/geographicAddressManagement/address';
    public const ELIGIBILITY                = '/oauth/customers/v1/eligibleActivationMigration';
    public const DOMAINS                    = '/b2b/configurationManagement/v1/config/sales/content';
    public const ORDER_APPROVAL             = '/b2b/salesProductOrdering/v2/order/approval';
    public const ORDER                      = '/b2b/salesProductOrdering/v2/productOrder';
    public const ORDER_STATUS_PROTOCOL      = '/b2b/salesProductOrdering/v1/productOrder/';
    public const ORDER_STATUS               = '/b2b2c/v1/order/status/';
    public const SIMCARD_ACTIVATION         = '/simCards/v1/hrdActivation';
    public const TRANSACTION_TOKEN          = '/b2b/partnerIntegration/v1/transactionToken';
    public const GENERATE_PROTOCOL          = '/interactions/v1/protocol';
    public const CUSTOMER_NUMBER_VALIDATION = '/b2b/customerGroupManagement/v1/customer/{{MSISDN}}/groupMemberValidation';
}

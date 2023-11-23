<?php

namespace ClaroBR\Tests\ServerTest;

final class ClaroBRResponseBook
{
    public const SUCCESS_AUTH                                = __DIR__ . '/Response/auth/successAuth.json';
    public const SUCCESS_AUTH_PROMOTER                       = __DIR__ . '/Response/promoterAuth/successPromoterAuth.json';
    public const SUCCESS_AUTH_PROMOTER_TWO_POINTS            = __DIR__ . '/Response/promoterAuth/successPromoterAuthTwoPoints.json';
    public const SUCCESS_AUTH_REQUEST_TOKEN                  = __DIR__ . '/Response/promoterAuth/successPromoterAuthSendAtoken.json';
    public const EXCEPTION_TOKEN_NOT_FOUND                   = __DIR__ . '/Response/promoterAuth/exceptionPromoterAuthTokenNotFound.json';
    public const SUCCESS_UTILS                               = __DIR__ . '/Response/utils/utils.json';
    public const USER_REGISTER                               = __DIR__ . '/Response/endpointUser/user.json';
    public const PDV_REGISTER                                = __DIR__ . '/Response/endpointUser/userPointOfSale.json';
    public const PLANS_AREA_CODE_11                          = __DIR__ . '/Response/listPlans/areaCode11.json';
    public const CLARO_BANDA_LARGA_PLANS                     = __DIR__ . '/Response/listPlans/claroBandaLargaPlans.json';
    public const CLARO_POS_PAGO_PLANS                        = __DIR__ . '/Response/listPlans/claroPosPagoPlans.json';
    public const CLARO_EMPTY_PLANS                           = __DIR__ . '/Response/listPlans/claroEmptyPlans.json';
    public const CLARO_BANDA_LARGA_SAVE_SALE                 = __DIR__ . '/Response/endpointSales/claroBandaLargaSaveSale.json';
    public const CLARO_BANDA_LARGA_QUERY_SALE                = __DIR__ . '/Response/endpointListSales/claroQuerySale.json';
    public const CLARO_CONTROLE_BOLETO_SAVE_SALE             = __DIR__ . '/Response/endpointSales/claroControleBoletoSaveSale.json';
    public const ERROR_ACTIVATION                            = __DIR__ . '/Response/endpointActivation/erroActivation.json';
    public const SELECT_MSISDN_ACTIVATION                    = __DIR__ . '/Response/endpointActivation/msisdnList.json';
    public const SUCCESS_ACTIVATION                          = __DIR__ . '/Response/endpointActivation/successActivation.json';
    public const SUCCESS_CREDIT_ANALYSIS                     = __DIR__ . '/Response/endpointCreditAnalysis/credit_analysis_success.json';
    public const SUCCESS_USER_LINES                          = __DIR__ . '/Response/endpointCreditAnalysis/user_lines_success.json';
    public const VALID_LIST_OF_REBATE                        = __DIR__ . '/Response/rebate/rebate_success-network-IPLACE.json';
    public const INVALID_REBATE                              = __DIR__ . '/Response/rebate/rebate_all-network-IPLACE.json';
    public const VALID_INDIVIDUAL_REBATE                     = __DIR__ . '/Response/rebate/rebate-individual.json';
    public const CONTEST_SUCCESS                             = __DIR__ . '/Response/contest/contestSuccess.json';
    public const CONTEST_ERROR                               = __DIR__ . '/Response/contest/contestError.json';
    public const UPDATE_SUCCESS                              = __DIR__ . '/Response/update/with_success.json';
    public const UPDATE_ERROR                                = __DIR__ . '/Response/update/with_error.json';
    public const AUTENTICA_SUCCESS                           = __DIR__ . '/Response/autentica/success.json';
    public const AUTENTICA_ERROR                             = __DIR__ . '/Response/autentica/error.json';
    public const AUTENTICA_SAVE_STATUS_SUCCESS               = __DIR__ . '/Response/autentica/save_autentica_success.json';
    public const AUTENTICA_SAVE_STATUS_ERROR                 = __DIR__ . '/Response/autentica/save_autentica_error.json';
    public const M4U_BY_ID_SUCCESS                           = __DIR__ . '/Response/m4u/m4uByIdSuccess.json';
    public const ICCID_SIMCARDS                              = __DIR__ . '/Response/iccid/iccidWithSimcards.json';
    public const ICCID_WITHOUT_SIMCARDS                      = __DIR__ . '/Response/iccid/iccidWithoutSimcards.json';
    public const SUCCESS_CHECK_AUTOMATIC_REGISTRATION_STATUS = __DIR__ . '/Response/cadastroAutomatico/claroCheckStatus.json';
    public const PLANS_WITH_VIABILITY                        = __DIR__ . '/Response/residentialPlans/PlansWithViability.json';
    public const PLANS_WITHOUT_VIABILITY                     = __DIR__ . '/Response/residentialPlans/PlansWithoutViability.json';
}

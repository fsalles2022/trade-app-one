<?php

namespace ClaroBR\Connection;

final class SivRoutes
{
    public const AUTH                                = 'auth';
    public const AUTH_PROMOTER                       = 'auth/promotor';
    public const ENDPOINT_USER                       = 'usuario';
    public const ENDPOINT_PDV_USER                   = 'usuario/pdv/';
    public const LIST_PLANS                          = 'plano';
    public const UTILS                               = 'util';
    public const ENDPOINT_SALES                      = 'venda';
    public const ENDPOINT_CREDIT_ANALYSIS            = 'servico/analise-credito';
    public const ENDPOINT_ACTIVATION                 = 'servico/ativar';
    public const CONTEST                             = 'servico/contestar';
    public const ENDPOINT_LIST_SALES                 = 'vendas';
    public const REBATE                              = 'rebate';
    public const POINT_OF_SALE_BY                    = 'pdv/por';
    public const ENDPOINT_AUTHENTICATE               = 'brscan/generate-auth-link';
    public const ENDPOINT_STATUS_AUTHENTICATE        = 'brscan/status-analysis';
    public const ENDPOINT_SAVE_STATUS_BRSCAN         = 'brscan/save-status';
    public const RESIDENTIAL                         = 'venda/fluxo-residencial';
    public const M4U_BY_SERVICE_ID                   = 'servico/m4u/';
    public const ENDPOINT_NEGADOS                    = 'servico/negados';
    public const ENDPOINT_ICCIDS                     = 'iccids';
    public const ENDPOINT_UPDATE_IMEI                = 'servico/imei';
    public const ENDPOINT_CHECK_PAYMENT              = 'm4u/check-status-payment';
    public const SEND_AUTOMATIC_REGISTRATION         = 'automatic-registration/create';
    public const CHECK_AUTOMATIC_REGISTRATION_STATUS = 'automatic-registration/check-status';
    public const RESIDENTIAL_PLANS_BY_CITY           = 'residencial/plan/city';
}

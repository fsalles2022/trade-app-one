<?php

namespace ClaroBR\Enumerators;

final class ClaroBRCaches
{
    public const USER_BEARER             = 'CLARO_USER_BEARER';
    public const PROMOTOR_ID             = 'CLARO_PROMOTOR_ID';
    public const AUTHENTICATION_DUE      = 200;
    public const CLARO_UTILS             = 'CLARO_UTILS';
    public const CLARO_DOMAINS           = 'CLARO_DOMAINS';
    public const UTILS_DUE               = 5;
    public const CLARO_PROMOTIONS        = 'CLARO_PROMOTIONS';
    public const PROMOTIONS_DUE          = 8;
    public const SIV_PROMOTER_FIRST_AUTH = 'SIV_PROMOTER_FIRST_AUTH';

    public const SIV3_USER_BEARER        = 'SIV3_USER_BEARER';
    public const SIV3_AUTHENTICATION_DUE = 360;
}

<?php

namespace McAfee\Enumerators;

class McAfeeStatusCode
{
    const TRANSACTION_SUCCESS                           = '1000';
    const INVALID_SCHEMA                                = '2001';
    const INVALID_DATA                                  = '2002';
    const INVALID_SKU                                   = '2003';
    const TRANSACTION_FAILED                            = '4000';
    const TRANSACTION_SUCCESS_EMAIL_EXISTS              = '5001';
    const TRANSACTION_SUCCESS_CONTEXT_FOR_ANOTHER_EMAIL = '5002';
}

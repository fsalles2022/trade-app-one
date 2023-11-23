<?php

declare(strict_types=1);

namespace SurfPernambucanas\Connection;

final class PagtelRoutes
{
    public const AUTHENTICATE        = 'api/Login';
    public const SUBSCRIBER_ACTIVATE = 'v3/SubscriberActivate';
    public const ALLOCATED_MSISDN    = 'v2/allocatedMSISDN';
    public const GET_VALUES          = 'v3/GetValues';
    public const GET_CARD            = 'v2/GetCard';
    public const ADD_CARD            = 'v2/addCard';
    public const RECHARGE            = 'v2/recharge';
    public const SUBMIT_PORTIN       = 'v2/submitPortin';
    public const PLANS               = 'v1/plans';
    public const ACTIVATIONS         = 'v1/activations';
}

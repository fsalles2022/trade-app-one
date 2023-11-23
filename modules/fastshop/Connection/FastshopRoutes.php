<?php

namespace FastShop\Connection;

final class FastshopRoutes
{
    public const AUTH          = 'm1/oauth/client_credential/accesstoken';
    public const LIST_PRODUCTS = 'v1/service_m1/getcatalogm1';
    public const DEVICE_PRICE  = 'v1/service_m1/getpricem1';
}

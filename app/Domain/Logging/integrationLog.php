<?php

function integrationLogger($message = ''): TradeAppOne\Domain\Logging\IntegrationLogger
{
    return new TradeAppOne\Domain\Logging\IntegrationConcrete($message);
}

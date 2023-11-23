<?php

function heimdallLog(): TradeAppOne\Domain\Logging\Heimdall\Heimdall
{
    return resolve(TradeAppOne\Domain\Logging\Heimdall\HeimdallConcrete::class);
}

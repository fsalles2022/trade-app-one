<?php


namespace TradeAppOne\Console\Commands;

use ClaroBR\Services\ClaroPromotionsService;
use Symfony\Component\Console\Command\Command;

class ClaroPromotionsUpdateCommand extends Command
{
    public function handle()
    {
        ClaroPromotionsService::updatePromotionsCached();
    }
}

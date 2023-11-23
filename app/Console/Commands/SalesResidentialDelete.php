<?php


namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Models\Collections\Sale;

class SalesResidentialDelete extends Command
{

    protected $signature   = 'residential:delete';
    protected $description = 'Delete residential sales imported from SIV';

    public function handle(): void
    {
        Sale::query()
            ->where('source', 'SIV')
            ->forceDelete();
        $this->info('Vendas deletadas');
    }
}

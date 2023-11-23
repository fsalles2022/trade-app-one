<?php


namespace Outsourced\ViaVarejo\Console;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;

class ViaVarejoSentinel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'viaVarejo:sentinel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search sale log status of ViaVarejo and Sync to activate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $sales = Sale::where('pointOfSale.network.slug', NetworkEnum::VIA_VAREJO)
            ->where('services.log.syncStatus', ServiceStatus::PENDING_SUBMISSION)->get();

        foreach ($sales as $sale) {
            foreach ($sale->services as $service) {
                if ($service instanceof Service) {
                    NetworkHooksFactory::run($service);
                }
            }
        }
    }
}

<?php

namespace Outsourced\GPA\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Outsourced\Enums\Outsourced;
use Outsourced\GPA\Adapters\Request\ActivationAdapter;
use Outsourced\GPA\Connections\GPAConnection;
use Outsourced\GPA\Models\GPA;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;

class GPASentinel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpa:sentinel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search status sale of GPA and Sync';

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
    public function handle()
    {
        $sales = Sale::where('services.retrySend', true)
            ->where('pointOfSale.network.slug', Outsourced::GPA)
            ->where('services.status', ServiceStatus::APPROVED)
            ->get();

        (! $sales->isEmpty())
            ? $this->fire($sales)
            : false;
    }

    private function fire(Collection $sales): void
    {
        $sales->each(static function (Sale $sale) {
            $sale->services->each(static function (Service $service) {
                self::send($service);
            });
        });
    }

    private static function send(Service $service): void
    {
        $payload  = (new ActivationAdapter($service))->toArray();
        $response = resolve(GPAConnection::class)->saleRegister($payload);
        $status   = $response->getStatus();

        ($status === Response::HTTP_CREATED)
            ? GPA::updateAttributes($service, ['retrySend' => false])
            : null;
    }
}

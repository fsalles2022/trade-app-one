<?php

namespace TradeAppOne\Console\Commands;

use Carbon\Carbon;
use ClaroBR\Connection\SivConnection;
use ClaroBR\Connection\VertexConnectionInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\RefusedSale;
use TradeAppOne\Domain\Models\Collections\Sale;

class MailingNegados extends Command
{
    protected $signature   = 'mailing:negados';
    protected $description = 'Mailing de vendas negadas';
    protected $sivConnection;
    protected $vertexConnection;
    protected $actualDate;
    protected $actualDateHourLate;

    public function __construct(SivConnection $sivConnection, VertexConnectionInterface $vertexConnection)
    {
        parent::__construct();
        $this->sivConnection      = $sivConnection;
        $this->vertexConnection   = $vertexConnection;
        $this->actualDate         = Carbon::now();
        $this->actualDateHourLate = Carbon::now()->subHour();
    }

    public function handle(): void
    {
        $this->info('Recuperando negados do SIV');
        $negados      = $this->getNegados();
        $vendasNextel = $this->getNextelSales();
        $resultant    = $this->removeNextelSales($negados, $vendasNextel);
        $this->info('Salvando registros');
        $this->store($resultant);
        $this->info('Enviando leads para Vertex');
        $response = $this->sendDataToVertex();
        $this->info($response->get());
    }

    public function getNegados(): Collection
    {
        $response = $this->sivConnection->getNegados()->get('data');
        return collect($response);
    }

    public function getNextelSales(): Collection
    {
        return Sale::query()
            ->where('services.operator', '=', Operations::NEXTEL)
            ->whereIn(
                'services.status',
                [
                    ServiceStatus::APPROVED,
                    ServiceStatus::ACCEPTED,
                    ServiceStatus::SUBMITTED
                ]
            )
            ->whereBetween('createdAt', [$this->actualDateHourLate, $this->actualDate])
            ->get()->pluck('services.*.msisdn')->flatten();
    }

    public function sendDataToVertex(): Responseable
    {
        $payload = $this->adaptPayload($this->getSalesFromYesterday());
        return $this->vertexConnection->sendData($payload);
    }

    private function store(Collection $info): void
    {
        $info->each(function ($value) {
            (new RefusedSale())->fill([
                'serviceId' => data_get($value, 'id'),
                'planType' => data_get($value, 'plano_tipo'),
                'clientName' => data_get($value, 'cliente_nome'),
                'clientCpf' => data_get($value, 'cliente_cpf'),
                'clientEmail' => data_get($value, 'cliente_email'),
                'clientNumber' => data_get($value, 'numero_acesso'),
                'referenceDate' => $this->actualDate->toDateTimeString()
            ])->save();
        });
    }

    private function getSalesFromYesterday()
    {
        return RefusedSale::query()
            ->whereBetween('createdAt', [$this->actualDateHourLate->subDay(), $this->actualDate->subDay()])
            ->get();
    }

    private function adaptPayload(Collection $sales): array
    {
        return $sales->map(static function ($item) {
            return [
                'name' => $item->clientName,
                'socialSecNum' => $item->clientCpf,
                'email' => $item->clientEmail,
                'msisdn' => $item->clientNumber,
            ];
        })->toArray();
    }

    private function removeNextelSales(Collection $refused, Collection $nextelSales): Collection
    {
        $nextelString = json_encode($nextelSales);
        return $refused->reject(static function ($value) use ($nextelString) {
            return $value['numero_acesso'] === null ||
                preg_match(sprintf("/\%s/", $value['numero_acesso']), $nextelString);
        });
    }
}

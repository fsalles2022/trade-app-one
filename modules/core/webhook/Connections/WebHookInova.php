<?php

namespace Core\WebHook\Connections;

use Core\WebHook\Adapters\WebHookServiceAdapter;
use Core\WebHook\Connections\Clients\WebHookHttpClient;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Network;

class WebHookInova implements WebHookConnection
{
    protected $client;

    public function __construct(WebHookHttpClient $client)
    {
        $this->client = $client;
    }

    public function push(Service $service, array $changes)
    {
        if ($this->isToSend($changes)) {
            $payload = WebHookServiceAdapter::map($service);
            $this->client->send($payload, NetworkEnum::INOVA);
        }
    }

    private function isToSend(array $changes): bool
    {
        return isset($changes['status']) || empty($changes);
    }

    public function isForMe(Service $service): bool
    {
        $networkId = data_get($service->sale, 'pointOfSale.network.id');
        $network   = Network::query()->findOrFail($networkId);
        $channels  = $network->channels->pluck('name');
        $channels->when($channels->count() === 0, static function ($channel) use ($network) {
            return $channel->push($network->channel);
        });

        $in = [
            Operations::LINE_ACTIVATION,
            Operations::TELECOMMUNICATION
        ];

        return $channels->contains(Channels::DISTRIBUICAO)
            && in_array($service->sector, $in, true);
    }
}

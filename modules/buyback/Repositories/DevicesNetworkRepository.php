<?php

namespace Buyback\Repositories;

use Buyback\Models\DevicesNetwork;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Tables\Device;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class DevicesNetworkRepository extends BaseRepository
{
    protected $model = DevicesNetwork::class;

    public function findByNetworkSlugAndPartialSku(string $networkSlug, string $partialSku): ?Collection
    {
        return $this->createModel()->whereHas('network', static function ($query) use ($networkSlug) {
            return $query->where('slug', $networkSlug);
        })->where('sku', 'like', "%$partialSku")->get();
    }

    public function findByNetworkSlugAndCompleteSku(string $networkSlug, string $sku): ?Collection
    {
        return $this->createModel()->whereHas('network', static function ($query) use ($networkSlug) {
            return $query->where('slug', $networkSlug);
        })->where('sku', $sku)->get();
    }

    public function getAllIpads(string $networkSlug): Builder
    {
        return $this->createModel()
        ->whereNotNull('sku')
        ->whereHas('device', function (Builder $query) {
            return $query->where('type', Device::TABLET_TYPE);
        })
        ->whereHas('network', function (Builder $query) use ($networkSlug) {
            return $query->where('slug', $networkSlug);
        });
    }
}

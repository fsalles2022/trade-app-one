<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;

class NetworkService
{
    public function filter($parameters): LengthAwarePaginator
    {
        return $this->basefilter($parameters)->paginate(10);
    }

    public function filterWithoutPaginate(array $parameters): Collection
    {
        return $this->basefilter($parameters)
            ->select()
            ->get();
    }

    private function basefilter(array $parameters): Builder
    {
        return NetworkRepository::getByFilter(array_filter($parameters));
    }

    public function find($id)
    {
        $network = NetworkRepository::findOneBy('id', $id)->first();

        if ($network instanceof Network) {
            return $network;
        }

        throw new NetworkNotFoundException();
    }

    public function findOneBySlug(?string $slug)
    {
        $network = NetworkRepository::findOneBy('slug', $slug)->first();

        if ($network instanceof Network) {
            return $network;
        }

        throw new NetworkNotFoundException();
    }
}

<?php

namespace Core\HandBooks\Http\Resources;

use Core\HandBooks\Http\Requests\HandbookFormRequest;
use Core\HandBooks\Repositories\HandbookRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HandbookListResource
{
    public static function list(HandbookFormRequest $request): Collection
    {
        $user    = $request->user();
        $filters = $request->validated();

        $handbooks = HandbookRepository::filter($user, $filters)->get();
        return self::adapter($handbooks);
    }

    public static function paginate(HandbookFormRequest $request, int $perPage = 10): LengthAwarePaginator
    {
        $user    = $request->user();
        $filters = $request->validated();

        $handbooks = HandbookRepository::filter($user, $filters)->paginate($perPage);
        $adapted   = self::adapter($handbooks->getCollection());

        return $handbooks->setCollection($adapted);
    }

    public static function adapter(Collection $collection): Collection
    {
        $handbooks = $collection->groupBy('module');

        foreach ($handbooks as $key => $handbook) {
            $module['label']     = trans("operations.$key");
            $module['handbooks'] = $handbook->groupBy('category');

            $handbooks[$key] = $module;
        }

        return $handbooks;
    }
}

<?php

namespace Core\HandBooks\Http\Resources;

use Core\HandBooks\Repositories\HandbookRepository;
use Core\HandBooks\Services\HandbookService;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\User;

class HandbookDomainsResource
{
    public static function all(User $user): Collection
    {
        $modules   = HandbookService::MODULES;
        $handbooks = HandbookRepository::filter($user)->get()->groupBy('module');
        $domains   = collect();

        foreach ($modules as $module) {
            $domain = [
                'id' => $module,
                'label' => trans('operations.'. $module),
                'categories' => []
            ];

            if (array_key_exists($module, $handbooks->toArray())) {
                $domain['categories'] = $handbooks[$module]->pluck('category')->unique()->values();
            }

            $domains->push($domain);
        }

        return $domains;
    }
}

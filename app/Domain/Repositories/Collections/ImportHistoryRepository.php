<?php

namespace TradeAppOne\Domain\Repositories\Collections;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\ImportHistory;
use TradeAppOne\Http\Controllers\Controller;

class ImportHistoryRepository extends Controller
{

    protected $importQuery;

    public function filterAndPaginate(Collection $roles, array $filters = [])
    {
        $rolesId           = $roles->pluck('id');
        $this->importQuery = ImportHistory::query();

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'firstName':
                    $this->importQuery->whereHas('user', function ($user) use ($value, $rolesId) {
                            $user->where('firstName', 'like', "%$value%");
                    });
                    break;

                case 'cpf':
                    $this->importQuery->whereHas('user', function ($user) use ($value, $rolesId) {
                            $user->where('cpf', 'like', "%$value%");
                    });
                    break;
            }
        }

        return $this->importQuery
            ->with('user')
            ->orderBy('createdAt', 'desc')
            ->whereHas('user', function ($user) use ($rolesId) {
                $user->whereIn('roleId', $rolesId);
            })->paginate(10);
    }

    public function find(int $id)
    {
        return ImportHistory::query()->find($id);
    }
}

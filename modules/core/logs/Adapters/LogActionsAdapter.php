<?php

namespace Core\Logs\Adapters;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Logging\Heimdall\HeimdallUserMapper;
use TradeAppOne\Domain\Models\Tables\BaseModel;

class LogActionsAdapter
{
    public const DATES = [
        BaseModel::CREATED_AT,
        BaseModel::UPDATED_AT,
        BaseModel::DELETED_AT,
        'startAt',
        'endAt'
    ];

    public static function get(BaseModel $model): array
    {
        return [
            'id'      => self::getId($model),
            'table'   => self::getTable($model),
            'model'   => self::getModel($model),
            'request' => self::getRequest(),
            'user'    => self::getUser(),
            'date'    => now()->toIso8601String()
        ];
    }

    public static function getId(BaseModel $model): string
    {
        $mount = [$model->getTable(), $model->getKey(), time()];
        return implode('_', $mount);
    }

    public static function getTable(BaseModel $model): array
    {
        return [
            'name' => $model->getTable(),
            'id'   => $model->getKey()
        ];
    }

    public static function getUser(): array
    {
        return HeimdallUserMapper::map(Auth::user());
    }

    public static function getModel(BaseModel $model): array
    {
        return [
            'original'    => json_encode($model->getOriginal()),
            'lazy_loaded' => json_encode($model->getRelations()),
            'changes'     => self::getChanges($model),
        ];
    }

    public static function getChanges(BaseModel $model): array
    {
        $changes = $model->getDirty();

        foreach (self::DATES as $date) {
            if (array_key_exists($date, $changes)) {
                $changes[$date] = Carbon::parse($changes[$date])->toIso8601String();
            }
        }

        return $changes;
    }

    public static function getRequest(): array
    {
        return [
            'agent'  => request()->headers->get('user-agent'),
            'body'   => json_encode(request()->all()),
            'method' => request()->getMethod(),
            'action' => request()->route() ? request()->route()->getActionMethod() : null,
            'uri'    => request()->getRequestUri()
        ];
    }
}

<?php

namespace Generali\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GeneraliProduct extends Model
{
    protected $connection = 'outsourced';
    protected $table      = 'generali_products';
    protected $hidden     = ['updatedAt', 'deletedAt'];

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    public const PRICE = 'Price';
    public const CODE  = 'Code';

    public const VALIDITY = [
        12 => 'twelveMonths',
        24 => 'twentyFourMonths'
    ];

    public function scopeRangeValue(Builder $query, array $parameters): Builder
    {
        return $query
                ->where('slug', $parameters['slug'])
                ->whereRaw("{$parameters['devicePrice']} BETWEEN startingTrack and finalTrack");
    }

    public static function getValidity(?string $value)
    {
        return self::VALIDITY[$value] ?? null;
    }
}

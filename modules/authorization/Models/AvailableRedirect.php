<?php

namespace Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer integrationId
 * @property string redirectUrl
 * @property boolean defaultUrl
 * @property string routeKey
 */
class AvailableRedirect extends Model
{
    protected $table      = 'available_redirects';
    protected $connection = 'outsourced';

    public $timestamps = false;

    protected $fillable = [
        'integrationId',
        'redirectUrl',
        'defaultUrl',
        'routeKey'
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class, 'integrationId');
    }

    public static function findByRouteKey(string $route): Model
    {
        return self::query()->where('routeKey', $route)->first();
    }
}

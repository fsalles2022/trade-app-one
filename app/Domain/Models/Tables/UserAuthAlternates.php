<?php

namespace TradeAppOne\Domain\Models\Tables;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property User user
 * @property string document
 * @property Carbon createdAt
 * @property Carbon updatedAt
 * @property Carbon deletedAt
 */
class UserAuthAlternates extends BaseModel
{
    protected $table = 'userAuthAlternates';

    protected $fillable = [
        'userId',
        'document'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}

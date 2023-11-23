<?php

declare(strict_types=1);

namespace Terms\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @property int $userId
 * @property int $termId
 * @property string $status
 */
class UserTerm extends BaseModel
{
    protected $table = 'userTerms';

    protected $fillable = [
        'userId',
        'termId',
        'status',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function term(): belongsTo
    {
        return $this->belongsTo(Term::class, 'termId');
    }
}

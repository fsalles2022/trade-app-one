<?php

declare(strict_types=1);

namespace Terms\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property string $title
 * @property string $urlEmbed
 * @property int $active
 * @property string $type
 */
class Term extends BaseModel
{
    protected $table = 'terms';

    protected $fillable = [
        'title',
        'urlEmbed',
        'active',
        'type'
    ];

    protected $hidden = [
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function userTerms(): HasMany
    {
        return $this->hasMany(UserTerm::class, 'termId', 'id');
    }
}

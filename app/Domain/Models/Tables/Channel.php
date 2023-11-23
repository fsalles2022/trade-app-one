<?php


namespace TradeAppOne\Domain\Models\Tables;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Channel extends BaseModel
{
    protected $hidden = [
        'pivot',
        'deletedAt'
    ];

    protected $fillable = [
        'name'
    ];

    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(Network::class, 'networks_channels', 'channelId', 'networkId');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_channels', 'channelId', 'userId');
    }
}

<?php

namespace TradeAppOne\Domain\Models\Tables;

use Bulletin\Models\Bulletin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Reports\Goals\Models\GoalType;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Portfolio\MongoAggregation;
use TradeAppOne\Domain\Services\AvailableServiceService;
use \Illuminate\Database\Eloquent\Collection as CollectionEloquent;

/**
 * @property int id
 * @property string slug
 * @property string cnpj
 * @property Collection channels
 * @property string availableServices
 * @property string timAuthentication
 */
class Network extends BaseModel implements MongoAggregation
{
    protected $fillable = [
        'label',
        'slug',
        'cnpj',
        'tradingName',
        'companyName',
        'telephone',
        'state',
        'city',
        'zipCode',
        'local',
        'neighborhood',
        'number',
        'complement',
        'preferences',
        'integrations',
        'channel'
    ];

    protected $table   = 'networks';
    protected $appends = ['availableServicesRelation'];

    public function pointsOfSale(): HasMany
    {
        return $this->hasMany(PointOfSale::class, 'networkId');
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'devices_network', 'networkId', 'deviceId')->withPivot(['isPreSale']);
    }

    public function hierarchies(): HasMany
    {
        return $this->hasMany(Hierarchy::class, 'networkId');
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'networks_channels', 'networkId', 'channelId');
    }

    public function availableServicesRelation(): HasMany
    {
        return $this->hasMany(AvailableService::class, 'networkId');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'availableServices', 'networkId', 'serviceId');
    }

    public function bulletins(): BelongsTo
    {
        return $this->belongsTo(Bulletin::class, 'bulletins', 'networkId');
    }

    /** @return Mixed[] */
    public function getTradeInMobileOperations(): array
    {
        return $this->services()->where([
            'sector'=> Operations::TRADE_IN,
            'operator' => Operations::TRADE_IN_MOBILE
        ])->get()->pluck('operation')->toArray();
    }

    public function toMongoAggregation(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'label' => $this->label,
        ];
    }

    public function goalsTypes(): BelongsToMany
    {
        return $this->belongsToMany(GoalType::class, 'network_goalsTypes', 'networkId', 'goalTypeId');
    }

    public function isMasterDealer(): bool
    {
        return $this->channel === Channels::MASTER_DEALER;
    }

    public function getAvailableServicesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function getPreferencesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    /** @return bool|array|null */
    public function getTimAuthentication()
    {
        return json_decode($this->timAuthentication, true);
    }

    public function getAvailableServicesRelationAttribute()
    {
        if ($this->services()->count()) {
            return AvailableServiceService::getOldFormatAvailableServices($this->services->toArray());
        }
        return null;
    }

    public function servicesClaro(): CollectionEloquent
    {
        return $this->services()->get()->filter(function (Service $service): bool {
            return $service->isOperatorClaro();
        });
    }
}

<?php

namespace TradeAppOne\Domain\Models\Tables;

use Bulletin\Models\Bulletin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Recommendation\Models\Recommendation;
use TradeAppOne\BulletinsPointOfSales;
use TradeAppOne\Domain\Services\AvailableServiceService;

/**
 * @property int id
 * @property string cnpj
 * @property Network network
 * @property Hierarchy $hierarchy
 * @property Collection services
 */
class PointOfSale extends BaseModel
{
    protected $table = 'pointsOfSale';

    protected $fillable = [
        'id',
        'label',
        'slug',
        'cnpj',
        'areaCode',
        'tradingName',
        'companyName',
        'telephone',
        'zipCode',
        'local',
        'neighborhood',
        'state',
        'number',
        'city',
        'complement',
        'latitude',
        'longitude',
        'providerIdentifiers',
        'networkId',
        'hierarchyId'
    ];

    protected $hidden = [
        'networkId',
        'userIds',
        'pivot'
    ];

    protected $appends = ['availableServicesRelation'];

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pointsOfSale_users', 'pointsOfSaleId', 'userId');
    }

    public function hierarchy(): BelongsTo
    {
        return $this->belongsTo(Hierarchy::class, 'hierarchyId');
    }

    public function availableServicesRelation(): HasMany
    {
        return $this->hasMany(AvailableService::class, 'pointOfSaleId');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'availableServices', 'pointOfSaleId', 'serviceId');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'pointOfSaleId');
    }

    public function getProviderIdentifiersAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function getAvailableServicesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function servicesClaro(): Collection
    {
        return $this->services()->get()->filter(function (Service $service): bool {
            return $service->isOperatorClaro();
        });
    }

    public function bulletins(): BelongsToMany
    {
        return $this->belongsToMany(Bulletin::class, 'bulletins_pointsOfSales', 'pointOfSaleId', 'bulletinId');
    }

    public function getAvailableServicesRelationAttribute()
    {
        if ($this->services()->count()) {
            return AvailableServiceService::getOldFormatAvailableServices($this->services->toArray());
        }
        return null;
    }

    public function getSlugNetwork(): string
    {
        return $this->network->slug;
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|min:1'
        ];
    }
}

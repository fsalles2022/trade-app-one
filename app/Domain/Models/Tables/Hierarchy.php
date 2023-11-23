<?php

namespace TradeAppOne\Domain\Models\Tables;

/**
 * @property $id
 * @property $slug
 * @property $label
 * @property $parent
 * @property $sequence
 * @property $networkId
 */
class Hierarchy extends BaseModel
{
    public $table = 'hierarchies';

    protected $fillable = [
        'slug',
        'label',
        'parent',
        'sequence',
        'networkId'
    ];

    public function rules(): array
    {
        return [
            'slug'  => 'required|unique:hierarchies,slug, '. $this->id . ',id',
            'label' => 'nullable'
        ];
    }

    public function pointsOfSale()
    {
        return $this->hasMany(PointOfSale::class, 'hierarchyId');
    }

    public function network()
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'hierarchies_users', 'hierarchyId', 'userId');
    }

    public function parentHierarchy()
    {
        return $this->belongsTo(Hierarchy::class, 'parent', 'id');
    }
}

<?php

namespace TradeAppOne\Domain\Models\Tables;

/**
 * @property string slug
 */
class Operator extends BaseModel
{
    public $table = 'operators';

    protected $fillable =[
        'slug',
        'label',
        'availableServices'
    ];

    public function rules(): array
    {
        return [
            'slug'  => 'required|unique:operators,slug, '. $this->id . ',id',
            'label' => 'nullable'
        ];
    }

    public function getAvailableServicesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'operators_users', 'operatorId', 'userId');
    }
}

<?php

namespace Buyback\Models;

use TradeAppOne\Domain\Models\Collections\BaseModel;

class OfferDeclined extends BaseModel
{
    protected $connection = 'mongodb';
    protected $collection = 'offerDeclined';

    protected $fillable = [
        'user',
        'pointOfSale',
        'customer',
        'device',
        'questions',
        'reason',
        'operator',
        'operation',
        'createdAt'
    ];

    protected $hidden = [
        '_id',
        'updatedAt',
    ];

    public function rules(): array
    {
        return [
            'user' => 'required',
            'pointOfSale' => 'required',
            'customer' => 'required',
            'customer.fullName' => 'required',
            'customer.email' => 'sometimes|required_without:customer.mainPhone',
            'customer.mainPhone' => 'sometimes|required_without:customer.email',
            'device' => 'required',
            'device.id' => 'required',
            'device.imei' => 'required|digits:15',
            'device.model' => 'required',
            'device.storage' => 'required',
            'device.color' => 'required',
            'device.price' => 'required',
            'device.note' => 'required',
            'questions' => 'required',
            'questions.*.id' => 'required',
            'questions.*.question' => 'required',
            'questions.*.weight' => 'required',
            'questions.*.answer' => 'required',
            'reason' => 'required',
            'operator' => 'required',
            'operation' => 'required',
        ];
    }
}

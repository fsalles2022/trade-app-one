<?php


namespace TradeAppOne\Domain\Models\Collections;

/**
 * Class RefusedSale
 * @property string clientCpf
 * @property string clientName
 * @property string clientNumber
 * @property string clientEmail
 */
class RefusedSale extends BaseModel
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'serviceId',
        'planType',
        'clientName',
        'clientCpf',
        'clientNumber',
        'clientEmail',
        'referenceDate'
    ];

    protected $hidden = [
        '_id',
        'createdAt',
        'updatedAt'
    ];
}

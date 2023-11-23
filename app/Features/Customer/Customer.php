<?php

namespace TradeAppOne\Features\Customer;

use TradeAppOne\Domain\Models\Collections\BaseModel;

/**
 * @property string cpf
 * @property string firstName
 * @property string lastName
 * @property string email
 * @property string zipCode
 * @property string birthday
 * @property string filiation
 * @property string rgLocal
 * @property string rgDate
 * @property string rgState
 * @property string gender
 * @property string mainPhone
 * @property string secondaryPhone
 * @property string rg
 * @property string number
 * @property string neighborhood
 * @property string local
 * @property string city
 * @property string state
 * @property string complement
 */
class Customer extends BaseModel
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'cpf',
        'firstName',
        'lastName',
        'email',
        'zipCode',
        'birthday',
        'filiation',
        'rgLocal',
        'rgDate',
        'rgState',
        'gender',
        'mainPhone',
        'secondaryPhone',
        'rg',
        'number',
        'neighborhood',
        'local',
        'city',
        'state',
        'complement',
    ];

    public function rules(): array
    {
        return [
            'cpf'            => 'required|cpf|numeric',
            'firstName'      => 'sometimes|name',
            'lastName'       => 'sometimes|name',
            'email'          => 'sometimes|email',
            'zipCode'        => 'sometimes|digits:8|numeric',
            'birthday'       => 'sometimes',
            'filiation'      => 'sometimes|name',
            'rgLocal'        => 'sometimes',
            'rgDate'         => 'sometimes',
            'rgState'        => 'sometimes',
            'gender'         => 'sometimes',
            'mainPhone'      => 'sometimes',
            'secondaryPhone' => 'sometimes',
            'rg'             => 'sometimes',
            'number'         => 'sometimes',
            'neighborhood'   => 'sometimes',
            'local'          => 'sometimes',
            'city'           => 'sometimes|name',
            'state'          => 'sometimes',
            'complement'     => 'sometimes',
        ];
    }
}

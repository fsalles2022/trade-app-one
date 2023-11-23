<?php

namespace TradeAppOne\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'firstName'              => $this->firstName,
            'lastName'               => $this->lastName,
            'cpf'                    => $this->cpf,
            'email'                  => $this->email,
            'areaCode'               => $this->areaCode,
            'integrationCredentials' => $this->integrationCredentials,
            'role'                   => $this->role->slug,
            'associative'            => $request
        ];
    }
}

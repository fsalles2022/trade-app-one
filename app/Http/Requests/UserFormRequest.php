<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Http\Request;

class UserFormRequest extends FormRequestAbstract
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (request()->getMethod() == Request::METHOD_POST) {
            return $this->onPost();
        }
        return $this->onPut();
    }

    public function onPost()
    {
        return [
            'firstName'   => 'required|string|name|min:2|max:255',
            'lastName'    => 'required|string|name|max:255',
            'email'       => 'required|string|email|max:255',
            'cpf'         => 'required|cpf|numeric|unique:users',
            'areaCode'    => 'sometimes|area_code_prefix|numeric',
            'pointOfSale' => 'required_without:hierarchy|numeric|exists:pointsOfSale,cnpj',
            'role'        => 'required|exists:roles,slug',
            'hierarchy'   => 'required_without:pointOfSale|string|exists:hierarchies,slug',
        ];
    }

    public function onPut()
    {
        return [
            'firstName'   => 'required|string|name|min:2|max:255',
            'lastName'    => 'required|string|name|max:255',
            'email'       => 'required|string|email|max:255',
            'areaCode'    => 'sometimes|area_code_prefix|numeric',
            'pointOfSale' => 'required_without:hierarchy|numeric|exists:pointsOfSale,cnpj',
            'role'        => 'required|exists:roles,slug',
            'hierarchy'   => 'required_without:pointOfSale|string|exists:hierarchies,slug',
        ];
    }
}

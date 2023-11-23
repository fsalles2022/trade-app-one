<?php

namespace TradeAppOne\Http\Requests;

class PointOfSaleFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'store':
                return $this->onPost();
                break;

            case 'edit':
                return $this->onPut();
                break;

            case 'index':
                return $this->onList();
                break;

            case 'updateByIntegration':
                return $this->updateByIntegration();

            default:
                return [];
        }
    }

    public function onPost()
    {
        return [
            'slug'         => 'bail|required|unique:pointsOfSale,slug|max:255',
            'label'        => 'bail|required|unique:pointsOfSale,label|max:255',
            'cnpj'         => 'bail|required|cnpj|unique:pointsOfSale,cnpj|max:14',
            'tradingName'  => 'required|max:255',
            'companyName'  => 'sometimes|max:255',
            'areaCode'     => 'sometimes|area_code_prefix|numeric',
            'telephone'    => 'sometimes|required|max:11',
            'state'        => 'required|states_br',
            'city'         => 'required',
            'zipCode'      => 'required',
            'local'        => 'required',
            'neighborhood' => 'required',
            'number'       => 'required',
            'complement'   => 'sometimes',
            'network.slug' => 'required|exists:networks,slug',
            'latitude'     => 'sometimes',
            'longitude'    => 'sometimes',
            'providerIdentifiers' => 'sometimes|required',
            'hierarchy.slug'    => 'required'
        ];
    }

    public function onPut()
    {
        return [
            'label'        => 'sometimes|required|max:255',
            'cnpj'         => 'bail|sometimes|required|cnpj|exists:pointsOfSale,cnpj|max:14',
            'tradingName'  => 'sometimes|required|max:255',
            'companyName'  => 'sometimes|required|max:255',
            'telephone'    => 'sometimes|required|max:11',
            'zipCode'      => 'required|max:8',
            'areaCode'     => 'sometimes|area_code_prefix|numeric',
            'local'        => 'sometimes|required',
            'neighborhood' => 'sometimes|required',
            'state'        => 'required|states_br|max:2',
            'number'       => 'sometimes|required',
            'city'         => 'sometimes|required',
            'complement'   => 'sometimes',
            'latitude'     => 'sometimes|required',
            'longitude'    => 'sometimes|required',
            'hierarchy.slug'    => 'required'
        ];
    }

    public function onList()
    {
        return [
            'cnpj'      => 'sometimes|max:14',
            'slug'      => 'sometimes',
            'state'     => 'sometimes|max:2',
            'operator'  => 'sometimes',
            'networks'  => 'sometimes|array|exists:networks,slug',
            'hierarchies'  => 'sometimes|array'
        ];
    }

    private function updateByIntegration(): array
    {
        return [
            'codigo'                   => 'required|string',
            'claro_autentica_promotor' => 'required|boolean',
            'chip_combo'               => 'required|boolean',
            'cf_lio'                   => 'required|boolean'
        ];
    }
}

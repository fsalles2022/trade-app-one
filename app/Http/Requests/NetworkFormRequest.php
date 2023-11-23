<?php

namespace TradeAppOne\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Channels;

class NetworkFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'store':
                return $this->onPost();
                break;

            case 'update':
                return $this->onPut();
                break;

            case 'index':
                return $this->onList();
                break;

            default:
                return [];
        }
    }

    private function onPost(): array
    {
        return [
            'slug'                           => 'bail|required|unique:networks,slug|max:255',
            'label'                          => 'bail|required|unique:networks,label|max:255',
            'cnpj'                           => 'bail|required|cnpj|unique:networks,cnpj|max:14',
            'tradingName'                    => 'required|max:255',
            'companyName'                    => 'required|max:255',
            'telephone'                      => 'sometimes|required|max:11',
            'zipCode'                        => 'required|string',
            'local'                          => 'required|string',
            'neighborhood'                   => 'required|string',
            'state'                          => 'required|states_br',
            'number'                         => 'sometimes|required',
            'city'                           => 'required|string',
            'complement'                     => 'sometimes',
            'channel'                        => ['required', Rule::in(Channels::AVAILABLE)],
            'availableServices'              => 'required|array'
        ];
    }

    private function onPut(): array
    {
        return [
            'label'                          => 'bail|required|unique:networks,label|max:255',
            'cnpj'                           => 'bail|required|cnpj|exists:networks,cnpj|max:14',
            'tradingName'                    => 'sometimes|required|max:255',
            'companyName'                    => 'sometimes|required|max:255',
            'telephone'                      => 'sometimes|required|max:11',
            'zipCode'                        => 'sometimes|required',
            'local'                          => 'sometimes|required',
            'neighborhood'                   => 'sometimes|required',
            'state'                          => 'sometimes|required|states_br',
            'number'                         => 'sometimes|required',
            'city'                           => 'sometimes|required',
            'complement'                     => 'sometimes',
        ];
    }

    private function onList(): array
    {
        return [
            'label'       => 'sometimes|nullable|max:255',
            'cnpj'        => 'sometimes|nullable|max:14',
            'tradingName' => 'sometimes|max:255',
            'companyName' => 'sometimes|max:255',
            'state'       => 'sometimes|required|states_br',
            'operator'    => 'sometimes|required',
        ];
    }
}

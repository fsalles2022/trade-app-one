<?php


namespace TradeAppOne\Http\Requests;

class OptionalSaleStepsRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sector'    => 'required|string',
            'operator'  => 'required|string',
            'operation' => 'required|string',
        ];
    }
}

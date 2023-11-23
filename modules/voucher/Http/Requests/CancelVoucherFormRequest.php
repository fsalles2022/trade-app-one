<?php


namespace Voucher\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class CancelVoucherFormRequest extends FormRequestAbstract
{
    public const METADATA_REQUIRED = 'metadata.required';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'metadata' => 'sometimes|required|array'
        ];
    }

    public function messages()
    {
        return [
            self::METADATA_REQUIRED  => trans('voucher::validate_messages.' . self::METADATA_REQUIRED)
        ];
    }
}

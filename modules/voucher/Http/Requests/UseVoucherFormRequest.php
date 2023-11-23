<?php


namespace Voucher\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class UseVoucherFormRequest extends FormRequestAbstract
{

    public const IMEI_REQUIRED     = 'imei.required';
    public const METADATA_REQUIRED = 'metadata.required';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'imei' => 'required|string|min:1|max:25',
            'metadata' => 'sometimes|required|array',
            'transactionId' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            self::IMEI_REQUIRED => trans('voucher::validate_messages.' . self::IMEI_REQUIRED),
            self::METADATA_REQUIRED  => trans('voucher::validate_messages.' . self::METADATA_REQUIRED)
        ];
    }
}

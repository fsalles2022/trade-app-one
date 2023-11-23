<?php

namespace TradeAppOne\Http\Requests;

class ImportFormRequest extends FormRequestAbstract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['file' => 'required|mimes:csv,txt,xlsx,xls|max:516'];
    }
}

<?php
declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class ViabilityFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        return [
            'serviceTransaction' => 'required'
        ];
    }
}

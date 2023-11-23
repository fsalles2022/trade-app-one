<?php

declare(strict_types=1);

namespace Terms\Http\Requests;

use Illuminate\Validation\Rule;
use Terms\Enums\StatusUserTermsEnum;
use Terms\Enums\TypeTermsEnum;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class TermFormRequest extends FormRequestAbstract
{
    public const GET_TERM   = 'getTerm';
    public const CHECK_TERM = 'checkTerm';

    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        $rules = [
            self::GET_TERM => $this->getTerm(),
            self::CHECK_TERM => $this->checkTerm()
        ];

        $action = $this->route()->getActionMethod();

        if (array_key_exists($action, $rules) === false) {
            return [];
        }

        return $rules[$action];
    }

    /** @return string[] */
    public function getTerm(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(TypeTermsEnum::TERM_TYPE)
            ]
        ];
    }

    /** @return string[] */
    public function checkTerm(): array
    {
        return [
            'termId' => 'required|int'
        ];
    }
}

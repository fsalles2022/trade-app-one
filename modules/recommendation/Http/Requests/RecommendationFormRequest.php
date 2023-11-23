<?php

namespace Recommendation\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class RecommendationFormRequest extends FormRequestAbstract
{
    private const GET_RECOMMENDATION = 'getRecommendation';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::GET_RECOMMENDATION:
                return $this->onGetRecommendation();
            default:
                return [];
        }
    }

    private function onGetRecommendation(): array
    {
        return [
            'registration' => 'required|string'
        ];
    }
}

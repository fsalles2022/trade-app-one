<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesSimulatorRequest extends FormRequest
{
    public const GET_PLANS_AND_PROMOTIONS_RESIDENTIALS = 'getPlansAndPromotions';

    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        return $this->getRules($this->route()->getActionMethod());
    }

    /** @return mixed[] */
    public function getRules(string $action): array
    {
        if (self::GET_PLANS_AND_PROMOTIONS_RESIDENTIALS === $action) {
            return $this->getPlansAndPromotionsRules();
        }
        return [];
    }

    /** @return string[] */
    public function getPlansAndPromotionsRules(): array
    {
        return [
            'zipCode'   => 'required|string'
        ];
    }
}

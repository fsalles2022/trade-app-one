<?php


namespace Buyback\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class WaybillFormRequest extends FormRequestAbstract
{
    public const GENERATE     = 'generate';
    public const AVAILABLE    = 'getAvailable';
    public const CHECK_DEVICE = 'checkDevice';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::GENERATE:
                return $this->onGenerate();
            case self::AVAILABLE:
                return $this->onAvailable();
            case self::CHECK_DEVICE:
                return $this->onCheckDevice();
            default:
                return [];
        }
    }

    private function onGenerate(): array
    {
        return [
            'cnpj' => 'required|exists:pointsOfSale,cnpj',
            'operation' => 'sometimes|required|string'
        ];
    }

    private function onAvailable(): array
    {
        return [
            'pointsOfSale' => 'array|nullable|exists:pointsOfSale,cnpj',
            'operations'   => ['array', 'nullable'],
        ];
    }

    /**
     * @return string[]
     */
    private function onCheckDevice(): array
    {
        return [
            'serviceTransaction' => 'required|string|size:20'
        ];
    }
}

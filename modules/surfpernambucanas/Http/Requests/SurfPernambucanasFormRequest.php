<?php

declare(strict_types=1);

namespace SurfPernambucanas\Http\Requests;

use TradeAppOne\Http\Requests\FormRequestAbstract;

class SurfPernambucanasFormRequest extends FormRequestAbstract
{
    public const SUBSCRIBER_ACTIVATE = 'subscriberActivate';
    public const ALLOCATE_MSISDN     = 'allocateMsisdn';
    public const PLANS               = 'plans';

    public function authorize(): bool
    {
        return true;
    }

    /** @return string[] */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        if ($action === self::SUBSCRIBER_ACTIVATE) {
            return $this->subscriberActivate();
        }

        if ($action === self::ALLOCATE_MSISDN) {
            return $this->allocateMsisdn();
        }

        if ($action === self::PLANS) {
            return $this->plans();
        }

        return [];
    }

    /** @return string[] */
    private function subscriberActivate(): array
    {
        return [
            'iccid'    => 'required|string|size:19',
            'areaCode' => 'required|integer|min:11|max:99',
            'cpf'      => 'required|string|size:11'
        ];
    }

    /** @return string[] */
    private function allocateMsisdn(): array
    {
        return [
            'iccid' => 'required|string|size:19'
        ];
    }

    /** @return string[] */
    private function plans(): array
    {
        return [
            'msisdn' => 'required|string|'
        ];
    }
}

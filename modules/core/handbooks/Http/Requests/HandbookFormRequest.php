<?php

namespace Core\HandBooks\Http\Requests;

use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class HandbookFormRequest extends FormRequestAbstract
{
    public const STORE     = 'store';
    public const INDEX     = 'index';
    public const PAGINATED = 'paginated';
    public const UPDATE    = 'update';

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::INDEX:
            case self::PAGINATED:
                return $this->onList();

            case self::STORE:
                return $this->onStore();

            case self::UPDATE:
                return $this->onUpdate();

            default:
                return [];
        }
    }

    private function onList(): array
    {
        return [
            'search'   => 'sometimes|string',
            'networks' => 'sometimes|array|exists:networks,slug',
            'roles'    => 'sometimes|array|exists:roles,slug'
        ];
    }

    private function onStore(): array
    {
        return [
            'title'              => 'required|string|min:2',
            'description'        => 'sometimes|string',
            'module'             => 'required|string',
            'category'           => 'required|string',
            'file'               => 'required|mimes:pdf,mp4|file_size',
            'networks'           => 'array|required_if:networksFilterMode,CHOSEN, CHOSEN|exists:networks,slug',
            'roles'              => 'array|required_if:rolesFilterMode,CHOSEN|exists:roles,slug',
            'networksFilterMode' => ['required', Rule::in(FilterModes::AVAILABLE)],
            'rolesFilterMode'    => ['required', Rule::in(FilterModes::AVAILABLE)],
        ];
    }

    private function onUpdate(): array
    {
        return [
            'title'              => 'sometimes|string|min:2',
            'description'        => 'sometimes|string',
            'file'               => 'sometimes|mimes:pdf,mp4|file_size',
            'networks'           => 'array|required_if:networksFilterMode,CHOSEN|exists:networks,slug',
            'roles'              => 'array|required_if:rolesFilterMode,CHOSEN|exists:roles,slug',
            'networksFilterMode' => ['sometimes', Rule::in(FilterModes::AVAILABLE)],
            'rolesFilterMode'    => ['sometimes', Rule::in(FilterModes::AVAILABLE)]
        ];
    }
}

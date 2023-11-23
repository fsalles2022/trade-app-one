<?php

namespace TradeAppOne\Http\Requests;

class HierarchyFormRequest extends FormRequestAbstract
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case 'postList':
                return $this->onList();
                break;
            case 'store':
                return $this->onPost();
                break;
            default:
                return [];
        }
    }

    private function onList(): array
    {
        return [
            'networks' => 'sometimes|array'
        ];
    }

    private function onPost(): array
    {
        return [
            'label'       => 'required|max:255',
            'parent'      => 'required',
            'slug'        => 'bail|required|unique:hierarchies,slug|max:255',
            'networkSlug' => 'required',
        ];
    }
}

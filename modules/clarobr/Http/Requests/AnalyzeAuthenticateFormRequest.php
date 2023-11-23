<?php


namespace ClaroBR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeAuthenticateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpf' => 'required|cpf',
        ];
    }
}

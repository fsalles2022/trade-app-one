<?php

namespace ClaroBR\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Formats;

class CreditAnalysisFormRequest extends FormRequest
{
    public const CREDIT_ANALYSIS = 'creditAnalysis';
    public const USER_LINES      = 'userLines';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): ?array
    {
        $action = $this->route()->getActionMethod();

        switch ($action) {
            case self::CREDIT_ANALYSIS:
                return $this->creditAnalysis();

            case self::USER_LINES:
                return $this->userLines();

            default:
                return [];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $content  = ['errors' => $validator->errors()];
        $response = response()->json($content, Response::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($response);
    }


    private function creditAnalysis(): array
    {
        return [
            'cpf'          => 'required|cpf',
            'firstName'    => 'required',
            'lastName'     => 'required',
            'birthday'     => 'required|date_format:"' . Formats::DATE . '"',
            'filiation'    => 'required|string',
            'city'         => 'required',
            'local'        => 'required',
            'neighborhood' => 'required',
            'state'        => 'required',
            'zipCode'      => 'required',
            'number'       => 'required',
        ];
    }

    private function userLines(): array
    {
        return [
            'cpf' => 'required|cpf'
        ];
    }
}

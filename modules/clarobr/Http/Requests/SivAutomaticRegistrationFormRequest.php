<?php

declare(strict_types=1);

namespace ClaroBR\Http\Requests;

use ClaroBR\Enumerators\ClaroDistributionOperations;
use ClaroBR\Rules\PhoneRule;
use Illuminate\Validation\Rule;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class SivAutomaticRegistrationFormRequest extends FormRequestAbstract
{
    public const AUTOMATIC_REGISTRATION = 'automaticRegistration';

    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        if ($action === self::AUTOMATIC_REGISTRATION) {
            return $this->automaticRegistration();
        }

        return [];
    }

    /** @return mixed[] */
    private function automaticRegistration(): array
    {
        return array_merge(
            $this->networkRules(),
            $this->centralizerRules(),
            $this->pointOfSaleRules(),
            $this->userRules()
        );
    }

    /** @return mixed[] */
    private function networkRules(): array
    {
        return [
            'rede'       => 'required|array',
            'rede.canal' => 'required|string',
            'rede.nome'  => 'required|string',
            'rede.cnpj'  => 'required|string|size:14',
        ];
    }

    /** @return mixed[] */
    private function centralizerRules(): array
    {
        return [
            'centralizador'             => 'sometimes|array',
            'centralizador.operacao'    => [
                'sometimes',
                'string',
                Rule::in([
                    ClaroDistributionOperations::OUT,
                    ClaroDistributionOperations::STRUCTURAL,
                ]),
            ],
        ];
    }

    /** @return mixed[] */
    private function pointOfSaleRules(): array
    {
        return [
            'pdv'                           => 'required|array',
            'pdv.nome'                      => 'required|string',
            'pdv.codigo'                    => 'required|string|min:2',
            'pdv.idpdv'                     => 'nullable|string',
            'pdv.cnpj'                      => 'required|string|size:14',
        ];
    }

    /** @return mixed[] */
    private function userRules(): array
    {
        return [
            'usuario'                       => 'required|array',
            'usuario.perfil'                 => 'required|string',
            'usuario.nome'                  => 'required|string',
            'usuario.cpf'                   => 'required|string|size:11',
            'usuario.email'                 => 'required|string|email',
            'usuario.telefone'              => ['required', new PhoneRule],
            'usuario.status'                => 'required|string',
            'usuario.data_nascimento'       => 'required|date_format:d/m/Y',
            'usuario.endereco'              => 'required|array',
            'usuario.endereco.cep'          => 'required|string|size:8',
            'usuario.endereco.uf'           => 'required|string|size:2',
            'usuario.endereco.cidade'       => 'required|string',
            'usuario.endereco.bairro'       => 'required|string',
            'usuario.endereco.rua'          => 'required|string',
            'usuario.endereco.numero'       => 'required|string',
            'usuario.endereco.complemento'  => 'nullable|string',
            'usuario.autenticacaoAlternativa' => 'sometimes|string'
        ];
    }
}

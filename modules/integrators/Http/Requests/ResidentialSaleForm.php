<?php


namespace Integrators\Http\Requests;

use ClaroBR\Enumerators\ClaroInvoiceTypes;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Http\Requests\FormRequestAbstract;

class ResidentialSaleForm extends FormRequestAbstract
{
    private const INVOICE_TYPES = [
        ClaroInvoiceTypes::CARTAO_CREDITO,
        ClaroInvoiceTypes::DEBITO_AUTOMATICO,
        ClaroInvoiceTypes::EMAIL,
        ClaroInvoiceTypes::VIA_POSTAL
    ];

    public function authorize(): bool
    {
        return true;
    }

    /** @return mixed[] */
    public function rules(): array
    {
        $defaultRules = [
            'id' => 'numeric|required',
            'source' => [Rule::in(SubSystemEnum::SIV), 'string', 'required'],
            'total' => 'string|required',
        ];

        return array_merge(
            $defaultRules,
            $this->userRules(),
            $this->servicesRules(),
            $this->posRules()
        );
    }

    /** @return mixed[] */
    public function userRules(): array
    {
        return [
            'user' => 'array|required',
            'user.id' => 'integer|required',
            'user.role' => 'string|required',
            'user.nome' => 'string|required',
            'user.cpf' => 'string|required|max:11',
            'user.email' => 'email|required'
        ];
    }

    /** @return mixed[] */
    public function servicesRules(): array
    {
        return [
            'services' => 'array|required',
            'services.*.id' => 'numeric|required',
            'services.*.plano' => 'string|required',
            'services.*.plano_id' => 'sometimes|required|numeric',
            'services.*.iccid' => 'nullable|string',
            'services.*.plano_tipo' => 'string|required',
            'services.*.operadora' => 'string|required',
            'services.*.tipo_servico' => 'string|required',
            'services.*.msidsn' => 'sometimes|required|string',
            'services.*.portedNumber' => 'sometimes|required|string',
            'services.*.vencimento' => 'sometimes|required|integer',
            'services.*.tipo_fatura' => ['sometimes', Rule::in(self::INVOICE_TYPES), 'string', 'required'],
            'services.*.banco_id' => 'nullable|required_if:services.tipo_fatura, DEBITO_AUTOMATICO|numeric',
            'services.*.agencia' => 'nullable|required_if:services.tipo_fatura, DEBITO_AUTOMATICO|numeric',
            'services.*.conta_corrente' => 'nullable|required_if:services.tipo_fatura, DEBITO_AUTOMATICO|numeric',
            'services.*.setor' => 'string|required',
            'services.*.status' => 'string|required',
            'services.*.valor' => 'string|required',
            'services.*.data_instalacao' => 'nullable|date',
            'services.*.numero_contrato' => 'nullable|string',
            'services.*.codigo_ibge' => 'nullable|string',
            'services.*.customer' => 'array|required',
            'services.*.customer.cpf' => 'string|required',
            'services.*.customer.nome' => 'string|required',
            'services.*.customer.data_nascimento' => 'string|required',
            'services.*.customer.email' => 'sometimes|required|email',
            'services.*.customer.telefone_principal' => 'sometimes|required|string',
            'services.*.customer.telefone_secundario' => 'sometimes|required|string',
            'services.*.customer.genero' => [Rule::in('M', 'F'), 'string', 'required'],
            'services.*.customer.filiacao' => 'string|required',
            'services.*.customer.cep' => 'string|required',
            'services.*.customer.uf' => 'string|required',
            'services.*.customer.logradouro' => 'string|required',
            'services.*.customer.cidade' => 'string|required',
            'services.*.customer.bairro' => 'string|required',
            'services.*.customer.numero' => 'string|required',
            'services.*.customer.complemento' => 'nullable|string'
        ];
    }

    /** @return mixed[] */
    public function posRules(): array
    {
        return [
            'pos' => 'array|required',
            'pos.id' => 'integer|required',
            'pos.slug' => 'string|required',
            'pos.codigo' => 'string|required',
            'pos.nome_fantasia' => 'string|required',
            'pos.cnpj' => 'string|required|max:15',
            'pos.razao_social' => 'string|required',
            'pos.uf' => 'string|required',
            'pos.cidade' => 'string|required',
            'pos.ddd' => 'integer|required',
            'pos.network' => 'array|required',
            'pos.network.id' => 'integer|required',
            'pos.network.nome' => 'string|required',
            'pos.network.slug' => 'string|required',
            'pos.network.label' => 'string|required',
            'pos.hierarchy' => 'array|required',
            'pos.hierarchy.nome' => 'string|required',
            'pos.hierarchy.slug' => 'string|required'
        ];
    }
}

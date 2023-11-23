<?php

use TradeAppOne\Domain\Rules\Validation\UndefinedValidation;

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted' => 'O :attribute deve ser aceito.',
    'active_url' => 'O :attribute não é uma URL válida.',
    'after' => 'O :attribute deve ser uma data posterior a :date.',
    'after_or_equal' => 'O data :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'O :attribute só pode conter letras.',
    'alpha_dash' => 'O :attribute só pode conter letras, números e hifens.',
    'alpha_num' => 'O :attribute só pode conter letras e números.',
    'array' => 'O :attribute deve ser um array.',
    'before' => 'O :attribute deve ser um data anterior a :date.',
    'before_or_equal' => 'O :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [
        'numeric' => 'O :attribute deve estar entre :min e :max.',
        'file' => 'O :attribute deve estar entre :min e :max kilobytes.',
        'string' => 'O :attribute deve estar entre :min e :max caracteres.',
        'array' => 'O :attribute deve estar entre :min e :max itens.',
    ],
    'boolean' => 'O campo :attribute deve ser true ou false.',
    'confirmed' => 'O :attribute de confirmação não confere.',
    'date' => 'O :attribute não é uma data válida.',
    'date_format' => 'O :attribute não tem formato igual a :format.',
    'different' => 'O :attribute e :other devem ser diferentes.',
    'digits' => 'O :attribute deve ser :digits dígitos.',
    'digits_between' => 'O :attribute deve estar entre :min e :max dígitos.',
    'dimensions' => 'O :attribute tem uma dimensão de imagem válida.',
    'distinct' => 'O campo :attribute tem valor duplicado.',
    'email' => 'O :attribute deve ser um endereço de e-mail válido.',
    'exists' => 'O valor :attribute não existe.',
    'file' => 'O :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve ter um valor.',
    'image' => 'O :attribute deve ser uma imagem.',
    'in' => 'O :attribute selecionado deve ser algum dos valores :values.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O :attribute deve ser um integer.',
    'ip' => 'O :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O :attribute deve ser um endereço IPV4 válido.',
    'ipv6' => 'O :attribute deve ser um endereço IPV6 válido.',
    'json' => 'O :attribute deve ser uma string JSON válida.',
    'max' => [
        'numeric' => 'O :attribute não pode ser maior que :max.',
        'file' => 'O :attribute não pode ser maior que :max kilobytes.',
        'string' => 'O :attribute não pode ser maior que :max caracteres.',
        'array' => 'O :attribute não pode ser maior que :max itens.',
    ],
    'mimes' => 'O :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O :attribute deve ser no mínimo :min.',
        'file' => 'O :attribute deve ser no mínimo :min kilobytes.',
        'string' => 'O :attribute deve ser no mínimo :min caracteres.',
        'array' => 'O :attribute deve ser no mínimo :min itens.',
    ],
    'not_in' => 'O :attribute selecionado é inválido.',
    'numeric' => 'O :attribute deve ser um número.',
    'present' => 'O campo :attribute deve estar presente.',
    'regex' => 'O formato do :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório  quando :other é :value.',
    'required_unless' => 'O :attribute é obrigatório a menos que :other esteja em :values.',
    'required_with' => 'O :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O :attribute é obrigatório quando :values está presente.',
    'required_without' => 'O :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O :attribute é obrigatório quando nenhum dos :values estão presentes.',
    'same' => 'O :attribute e :other devem ser iguais.',
    'size' => [
        'numeric' => 'O :attribute deve ser :size.',
        'file' => 'O :attribute deve ser :size kilobytes.',
        'string' => 'O :attribute deve ser :size caracteres.',
        'array' => 'O :attribute deve conter :size itens.',
    ],
    'string' => 'O :attribute deve ser uma string.',
    'timezone' => 'O :attribute deve ser uma zona válida.',
    'unique' => 'O :attribute já existe.',
    'uploaded' => 'O :attribute falhou no upload.',
    'url' => 'O :attribute tem formato inválido.',
    'states_br' => 'Sigla de Estado Brasileiro inválida',
    'permissions' => 'Você pode conceder somente as permissões que você já possui',
    'has_operator_permission' => 'Essa rede não tem permissão para esta operação.',
    /*
    |--------------------------------------------------------------------------
    | User Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'cpf' => 'Número de CPF inválido.',
    'cnpj' => 'Número de CNPJ inválido.',
    'area_code_prefix' => 'Número de DDD inválido.',
    'password' => 'Formato de senha inválido.',
    'point_of_sale' => 'Ponto de vendas inválido',
    'name' => 'Nome inválido',
    'file_size' => 'O arquivo excedeu o tamanho máximo permitido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [],
    UndefinedValidation::KEY => 'Instabilidade na operação, efetue o processo novamente.'
];

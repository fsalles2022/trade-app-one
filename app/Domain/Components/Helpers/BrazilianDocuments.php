<?php

namespace TradeAppOne\Domain\Components\Helpers;

use Illuminate\Support\Facades\Validator;

class BrazilianDocuments
{
    public static function validateCpf(string $cpf): string
    {
        $cpf         = self::unmask($cpf);
        $formatedCpf = str_pad(trim($cpf), 11, "0", STR_PAD_LEFT);
        $validation  = Validator::make(['cpf' => $formatedCpf], ['cpf' => 'cpf']);
        throw_if($validation->fails(), new \InvalidArgumentException($validation->errors()->first()));
        return $formatedCpf;
    }

    public static function unmask(string $value)
    {
        return str_replace(['-', '.', '/'], '', $value);
    }

    public static function validateCnpj(string $cnpj): string
    {
        $cnpj        = self::unmask($cnpj);
        $formatedCpf = str_pad(trim($cnpj), 14, "0", STR_PAD_LEFT);
        $validation  = Validator::make(['cnpj' => $formatedCpf], ['cnpj' => 'cnpj']);
        throw_if($validation->fails(), new \InvalidArgumentException($validation->errors()->first()));
        return $formatedCpf;
    }
}

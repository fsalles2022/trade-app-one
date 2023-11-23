<?php

namespace VivoBR\Adapters\Request;

use Carbon\Carbon;
use Faker\Factory;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use VivoBR\Enumerators\SunFormats;

class UserRequestAdapter
{
    public static function adapt(User $user, PointOfSale $pointOfSale): array
    {
        $birthday = $user->birthday;
        if ($birthday) {
            $birthday = Carbon::parse($birthday)->format(SunFormats::DATE_FORMAT);
        } else {
            $birthday = Factory::create()->dateTimeBetween(18)->format(SunFormats::DATE_FORMAT);
        }
        $cnpj = $pointOfSale->cnpj;
        if ($cnpj) {
            return array_filter([
                'identificacao'  => $user->cpf,
                'funcao'         => SunFormats::VENDEDOR_ROLE,
                'nome'           => $user->firstName . ' ' . $user->lastName,
                'cpf'            => $user->cpf,
                'dataNascimento' => $birthday,
                'logradouro'     => '',
                'bairro'         => '',
                'cep'            => '',
                'numero'         => '',
                'complemento'    => '',
                'email'          => '',
                'telefone'       => '',
                'cnpj'           => $cnpj
            ]);
        } else {
            throw  new \InvalidArgumentException();
        }
    }
}

<?php

namespace TradeAppOne\Tests\Helpers;

trait ControleFacilHelper
{
    public $controleFacilCustomer = [
        'email' => 'teste',
        'firstName' => 'teste',
        'lastName' => 'teste',
        'cpf' => 'teste',
    ];

    public function getControleFacilFilled()
    {
        return [
            'operator' => 'CLARO',
            'operation' => 'CONTROLE_FACIL',
            'product' => '',
            'msisdn' => '',
            'customer' => [
                'email' => 'teste',
                'firstName' => 'teste',
                'lastName' => 'teste',
                'cpf' => 'teste',
            ]
        ];
    }
}

<?php

namespace TradeAppOne\Tests\Helpers;

trait ControleBoletoHelper
{
    public $controleBoletoCustomer = [
        'email' => 'teste',
        'firstName' => 'teste',
        'lastName' => 'teste',
        'cpf' => 'teste',
        'gender' => 'teste',
        'birthday' => 'teste',
        'filiation' => 'teste',
        'mainPhone' => 'teste',
        'secondaryPhone' => 'teste',
        'salaryRange' => 'teste',
        'profession' => 'teste',
        'maritalStatus' => 'teste',
        'rg' => 'teste',
        'rgLocal' => 'teste',
        'rgDate' => 'teste',
        'number' => 'teste',
        'zipCode' => 'teste',
        'neighborhood' => 'teste',
        'neighborhoodType' => 'teste',
        'local' => 'teste',
        'localId' => 'teste',
        'city' => 'teste',
        'state' => '',
    ];

    public function getControleBoletoFilled()
    {
        return ['operator' => 'CLARO',
            'operation' => 'CONTROLE_BOLETO',
            'msisdn' => '',
            'dueDate' => '',
            'invoiceType' => '',
            'areaCode' => '',
            'customer' => ['email' => 'teste',
                'firstName' => 'teste',
                'lastName' => 'teste',
                'cpf' => 'teste',
                'gender' => 'teste',
                'birthday' => 'teste',
                'filiation' => 'teste',
                'mainPhone' => 'teste',
                'secondaryPhone' => 'teste',
                'salaryRange' => 'teste',
                'profession' => 'teste',
                'maritalStatus' => 'teste',
                'rg' => 'teste',
                'rgLocal' => 'teste',
                'rgDate' => 'teste',
                'number' => 'teste',
                'zipCode' => 'teste',
                'neighborhood' => 'teste',
                'neighborhoodType' => 'teste',
                'local' => 'teste',
                'localId' => 'teste',
                'city' => 'teste',
                'state' => '',]];
    }
}

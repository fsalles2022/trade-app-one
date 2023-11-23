<?php

return [
    'areaCode'      => 'ddd',
    'user'          => [
        'firstName'   => 'nome',
        'lastName'    => 'sobrenome',
        'pointOfSale' => 'pontoDeVenda',
        'birthday'    => 'dataDeNascimento',
        'role'        => 'funcao',
        'network'     => 'rede',
        'hierarchy'   => 'regional',
        'matriculation'   => 'matricula'
    ],
    'network'       => 'rede',
    'point_of_sale' => 'pontoDeVenda',
    'hierarchy'     => [
        'slug'      => 'regional'
    ],
    'point_of_sale' => [
        'tradingName' => 'razaoSocial',
        'companyName' => 'nomeFantasia',
        'cnpj'        => 'pontoDeVenda',
    ],
    'address'       => [
        'state'        => 'estado',
        'city'         => 'cidade',
        'zipCode'      => 'cep',
        'neighborhood' => 'bairro',
        'local'        => 'logradouro',
        'number'       => 'numero',
        'longitude'    => 'longitude',
        'latitude'     => 'latitude',
    ],
    'buyback'       => [
        'evaluation' => [
            'quizId'        => 'Formulario',
            'deviceId'      => 'Aparelho',
            'goodValue'     => 'precoBom',
            'sponsor'       => 'nomePatrocinador',
            'averageValue'  => 'precoMedio',
            'defectValue'   => 'precoDefeituoso'
        ]
    ],
    'timRebate' => [
        'externalCode' => 'CÓDIGO',
        'brand' => 'FABRICANTE',
        'model' => 'DESCRIÇÃO SAP',
        'commercialModel' => 'DESCRIÇÃO COMERCIAL',
        'price' => 'PREÇO AVULSO APARELHO',
    ]
];

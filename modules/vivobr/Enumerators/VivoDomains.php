<?php


namespace VivoBR\Enumerators;

class VivoDomains
{
    public const DUE_DATES = [
        ['value' => 1, 'label' => 'Dia 1'],
        ['value' => 6, 'label' => 'Dia 6'],
        ['value' => 17, 'label' => 'Dia 17'],
        ['value' => 21, 'label' => 'Dia 21'],
        ['value' => 26, 'label' => 'Dia 26']
    ];

    public const BANKS = [
        ['value' => 1, 'label' => 'Banco do Brasil S/A'],
        ['value' => 3, 'label' => 'Bancoob S/A'],
        ['value' => 5, 'label' => 'Banestes S/A'],
        ['value' => 6, 'label' => 'Banrisul'],
        ['value' => 8, 'label' => 'Bradesco S/A'],
        ['value' => 9, 'label' => 'Caixa Economica Fed',
            'types' => [
                ['value' => 1, 'label' => 'Conta Corrente'],
                ['value' => 2, 'label' => 'Conta Simples'],
                ['value' => 3, 'label' => 'Pessoa Jurídica'],
                ['value' => 13, 'label' => 'Conta Poupança'],
            ]
        ],
        ['value' => 12, 'label' => 'Itau S/A'],
        ['value' => 13, 'label' => 'Mercantil do Brasil'],
        ['value' => 14, 'label' => 'Safra'],
        ['value' => 16, 'label' => 'Santander'],
        ['value' => 17, 'label' => 'BANPARA'],
        ['value' => 19, 'label' => 'Sicred'],
    ];

    public const ANOTHER_PORTABILITY_OPERATORS = [
        ['value' => 1, 'label' => 'Claro'],
        ['value' => 2, 'label' => 'Tim'],
        ['value' => 3, 'label' => 'Oi'],
        ['value' => 4, 'label' => 'Nextel'],
        ['value' => 5, 'label' => 'NET'],
        ['value' => 6, 'label' => 'Outros'],
        ['value' => 7, 'label' => 'Vivo'],
        ['value' => 8, 'label' => 'CTBC'],
    ];
}

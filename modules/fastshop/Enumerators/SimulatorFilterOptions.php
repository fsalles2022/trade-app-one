<?php

namespace FastShop\Enumerators;

class SimulatorFilterOptions
{

    private const LABEL_CHOICES = 'choices';
    private const LABEL_ID      = 'id';
    private const LABEL_NAME    = 'name';
    private const LABEL_TYPE    = 'type';
    private const LABEL_VALUE   = 'value';

    private const TYPE_SELECT          = 'select';
    private const TYPE_SELECT_MULTIPLE = 'select_multiple';

    public const PLAN_POS_PAGO = 'pos-pago';
    public const PLAN_CONTROLE = 'controle';
    public const PLAN_PRE_PAGO = 'pre-pago';

    public const FILTERS = [
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => self::PLAN_POS_PAGO, self::LABEL_NAME => 'Pós-pago'],
                [self::LABEL_ID => self::PLAN_CONTROLE, self::LABEL_NAME => 'Controle'],
                [self::LABEL_ID => self::PLAN_PRE_PAGO, self::LABEL_NAME => 'Pré-pago'],
            ],
            self::LABEL_ID      => 'tipo_plano',
            self::LABEL_NAME    => 'Tipo de Plano',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 'pos-pago'
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => '11', self::LABEL_NAME => '11'],
                [self::LABEL_ID => '12', self::LABEL_NAME => '12'],
                [self::LABEL_ID => '13', self::LABEL_NAME => '13'],
                [self::LABEL_ID => '14', self::LABEL_NAME => '14'],
                [self::LABEL_ID => '15', self::LABEL_NAME => '15'],
                [self::LABEL_ID => '16', self::LABEL_NAME => '16'],
                [self::LABEL_ID => '17', self::LABEL_NAME => '17'],
                [self::LABEL_ID => '18', self::LABEL_NAME => '18'],
                [self::LABEL_ID => '19', self::LABEL_NAME => '19'],
                [self::LABEL_ID => '21', self::LABEL_NAME => '21'],
                [self::LABEL_ID => '22', self::LABEL_NAME => '22'],
                [self::LABEL_ID => '24', self::LABEL_NAME => '24'],
                [self::LABEL_ID => '27', self::LABEL_NAME => '27'],
                [self::LABEL_ID => '28', self::LABEL_NAME => '28'],
                [self::LABEL_ID => '31', self::LABEL_NAME => '31'],
                [self::LABEL_ID => '32', self::LABEL_NAME => '32'],
                [self::LABEL_ID => '33', self::LABEL_NAME => '33'],
                [self::LABEL_ID => '34', self::LABEL_NAME => '34'],
                [self::LABEL_ID => '35', self::LABEL_NAME => '35'],
                [self::LABEL_ID => '37', self::LABEL_NAME => '37'],
                [self::LABEL_ID => '38', self::LABEL_NAME => '38'],
                [self::LABEL_ID => '41', self::LABEL_NAME => '41'],
                [self::LABEL_ID => '42', self::LABEL_NAME => '42'],
                [self::LABEL_ID => '43', self::LABEL_NAME => '43'],
                [self::LABEL_ID => '44', self::LABEL_NAME => '44'],
                [self::LABEL_ID => '45', self::LABEL_NAME => '45'],
                [self::LABEL_ID => '46', self::LABEL_NAME => '46'],
                [self::LABEL_ID => '47', self::LABEL_NAME => '47'],
                [self::LABEL_ID => '48', self::LABEL_NAME => '48'],
                [self::LABEL_ID => '49', self::LABEL_NAME => '49'],
                [self::LABEL_ID => '51', self::LABEL_NAME => '51'],
                [self::LABEL_ID => '53', self::LABEL_NAME => '53'],
                [self::LABEL_ID => '54', self::LABEL_NAME => '54'],
                [self::LABEL_ID => '55', self::LABEL_NAME => '55'],
                [self::LABEL_ID => '61', self::LABEL_NAME => '61'],
                [self::LABEL_ID => '62', self::LABEL_NAME => '62'],
                [self::LABEL_ID => '63', self::LABEL_NAME => '63'],
                [self::LABEL_ID => '64', self::LABEL_NAME => '64'],
                [self::LABEL_ID => '65', self::LABEL_NAME => '65'],
                [self::LABEL_ID => '66', self::LABEL_NAME => '66'],
                [self::LABEL_ID => '67', self::LABEL_NAME => '67'],
                [self::LABEL_ID => '68', self::LABEL_NAME => '68'],
                [self::LABEL_ID => '69', self::LABEL_NAME => '69'],
                [self::LABEL_ID => '71', self::LABEL_NAME => '71'],
                [self::LABEL_ID => '73', self::LABEL_NAME => '73'],
                [self::LABEL_ID => '74', self::LABEL_NAME => '74'],
                [self::LABEL_ID => '75', self::LABEL_NAME => '75'],
                [self::LABEL_ID => '77', self::LABEL_NAME => '77'],
                [self::LABEL_ID => '79', self::LABEL_NAME => '79'],
                [self::LABEL_ID => '81', self::LABEL_NAME => '81'],
                [self::LABEL_ID => '82', self::LABEL_NAME => '82'],
                [self::LABEL_ID => '83', self::LABEL_NAME => '83'],
                [self::LABEL_ID => '84', self::LABEL_NAME => '84'],
                [self::LABEL_ID => '85', self::LABEL_NAME => '85'],
                [self::LABEL_ID => '86', self::LABEL_NAME => '86'],
                [self::LABEL_ID => '87', self::LABEL_NAME => '87'],
                [self::LABEL_ID => '88', self::LABEL_NAME => '88'],
                [self::LABEL_ID => '89', self::LABEL_NAME => '89'],
                [self::LABEL_ID => '91', self::LABEL_NAME => '91'],
                [self::LABEL_ID => '92', self::LABEL_NAME => '92'],
                [self::LABEL_ID => '93', self::LABEL_NAME => '93'],
                [self::LABEL_ID => '94', self::LABEL_NAME => '94'],
                [self::LABEL_ID => '95', self::LABEL_NAME => '95'],
                [self::LABEL_ID => '96', self::LABEL_NAME => '96'],
                [self::LABEL_ID => '97', self::LABEL_NAME => '97'],
                [self::LABEL_ID => '98', self::LABEL_NAME => '98'],
                [self::LABEL_ID => '99', self::LABEL_NAME => '99'],
            ],
            self::LABEL_ID      => 'ddd',
            self::LABEL_NAME    => 'DDD',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => '11'
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 'Claro', self::LABEL_NAME => 'Claro'],
                [self::LABEL_ID => 'Nextel', self::LABEL_NAME => 'Nextel'],
                [self::LABEL_ID => 'Tim', self::LABEL_NAME => 'Tim'],
                [self::LABEL_ID => 'Vivo', self::LABEL_NAME => 'Vivo'],
            ],
            self::LABEL_ID      => 'operadoras',
            self::LABEL_NAME    => 'Operadoras',
            self::LABEL_TYPE    => self::TYPE_SELECT_MULTIPLE,
            self::LABEL_VALUE   => []
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 100, self::LABEL_NAME => 100],
                [self::LABEL_ID => 200, self::LABEL_NAME => 200],
                [self::LABEL_ID => 400, self::LABEL_NAME => 400],
                [self::LABEL_ID => 500, self::LABEL_NAME => 500],
                [self::LABEL_ID => 600, self::LABEL_NAME => 600],
                [self::LABEL_ID => 800, self::LABEL_NAME => 800],
                [self::LABEL_ID => 1200, self::LABEL_NAME => 1200],
            ],
            self::LABEL_ID      => 'minutos',
            self::LABEL_NAME    => 'Minutos',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 100
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 1000, self::LABEL_NAME => 1000],
                [self::LABEL_ID => 3000, self::LABEL_NAME => 3000],
                [self::LABEL_ID => 5000, self::LABEL_NAME => 5000],
                [self::LABEL_ID => 10000, self::LABEL_NAME => 10000],
                [self::LABEL_ID => 15000, self::LABEL_NAME => 15000],
                [self::LABEL_ID => 20000, self::LABEL_NAME => 20000],
                [self::LABEL_ID => 25000, self::LABEL_NAME => 25000],
            ],
            self::LABEL_ID      => 'internet',
            self::LABEL_NAME    => 'Internet',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 1000
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => '', self::LABEL_NAME => 'Não, quero um novo número'],
                [self::LABEL_ID => 'Claro', self::LABEL_NAME => 'Sim, operadora atual Claro'],
                [self::LABEL_ID => 'Nextel', self::LABEL_NAME => 'Sim, operadora atual Nextel'],
                [self::LABEL_ID => 'Oi', self::LABEL_NAME => 'Sim, operadora atual Oi'],
                [self::LABEL_ID => 'Porto Conecta', self::LABEL_NAME => 'Sim, operadora atual Porto Conecta'],
                [self::LABEL_ID => 'Tim', self::LABEL_NAME => 'Sim, operadora atual Tim'],
                [self::LABEL_ID => 'Vivo', self::LABEL_NAME => 'Sim, operadora atual Vivo'],
            ],
            self::LABEL_ID      => 'operadora_atual',
            self::LABEL_NAME    => 'Deseja manter seu número?',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => ''
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 0, self::LABEL_NAME => 0],
                [self::LABEL_ID => 1, self::LABEL_NAME => 1],
                [self::LABEL_ID => 2, self::LABEL_NAME => 2],
                [self::LABEL_ID => 3, self::LABEL_NAME => 3],
                [self::LABEL_ID => 4, self::LABEL_NAME => 4],
                [self::LABEL_ID => 5, self::LABEL_NAME => 5],
            ],
            self::LABEL_ID      => 'dependentes_internet',
            self::LABEL_NAME    => 'Dependentes Internet',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 0
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 0, self::LABEL_NAME => 0],
                [self::LABEL_ID => 1, self::LABEL_NAME => 1],
                [self::LABEL_ID => 2, self::LABEL_NAME => 2],
                [self::LABEL_ID => 3, self::LABEL_NAME => 3],
                [self::LABEL_ID => 4, self::LABEL_NAME => 4],
                [self::LABEL_ID => 5, self::LABEL_NAME => 5],
            ],
            self::LABEL_ID      => 'dependentes_voz_internet',
            self::LABEL_NAME    => 'Dependentes Voz Internet',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 0
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 0, self::LABEL_NAME => 0],
                [self::LABEL_ID => 1, self::LABEL_NAME => 1],
                [self::LABEL_ID => 2, self::LABEL_NAME => 2],
                [self::LABEL_ID => 3, self::LABEL_NAME => 3],
                [self::LABEL_ID => 4, self::LABEL_NAME => 4],
                [self::LABEL_ID => 5, self::LABEL_NAME => 5],
            ],
            self::LABEL_ID      => 'dependentes_voz',
            self::LABEL_NAME    => 'Dependentes Voz',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 0
        ],
        [
            self::LABEL_CHOICES => [
                [self::LABEL_ID => 0, self::LABEL_NAME => 0],
                [self::LABEL_ID => 1, self::LABEL_NAME => 1]
            ],
            self::LABEL_ID      => 'debito_automatico',
            self::LABEL_NAME    => 'Debito automático',
            self::LABEL_TYPE    => self::TYPE_SELECT,
            self::LABEL_VALUE   => 0
        ]
    ];
}

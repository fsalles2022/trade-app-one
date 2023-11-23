<?php

namespace Reports\Tests\Fixture;

class HourlyLayoutFixture
{
    public static function getSaleWithPrice()
    {
        return
            array (
                'DATE' => '02/01/2020',
                'TIME' => '17:31:12',
                'NETWORK' => 'Prof. Faye Wolff I',
                'HEADERS' =>
                    array (
                        'RESUME' =>
                            array (
                                'TOTAL' =>
                                    array (
                                        'QUANTITY' => 554,
                                        'PERCENT' => '100%',
                                        'VALUES' => 24716.760000000006
                                    ),
                            ),
                        'POS_PAGO' =>
                            array (
                                'CLARO' =>
                                    array (
                                        'QUANTITY' => 208,
                                        'PERCENT' => 44.53999999999999914734871708787977695465087890625,
                                    ),
                                'NEXTEL' =>
                                    array (
                                        'QUANTITY' => 0,
                                        'PERCENT' => 0,
                                    ),
                                'OI' =>
                                    array (
                                        'QUANTITY' => 36,
                                        'PERCENT' => 7.70999999999999996447286321199499070644378662109375,
                                    ),
                                'TIM' =>
                                    array (
                                        'QUANTITY' => 88,
                                        'PERCENT' => 18.839999999999999857891452847979962825775146484375,
                                    ),
                                'VIVO' =>
                                    array (
                                        'QUANTITY' => 135,
                                        'PERCENT' => 28.910000000000000142108547152020037174224853515625,
                                    ),
                                'TOTAL' =>
                                    array (
                                        'QUANTITY' => 467,
                                        'PERCENT' => 84.3,
                                    ),
                                'DMINUS3' =>
                                    array (
                                        'QUANTITY' => 14,
                                        'PERCENT' => '29/12',
                                    ),
                                'DAY' =>
                                    array (
                                        'QUANTITY' => 7,
                                        'PERCENT' => '02/01',
                                    ),
                            ),
                        'PRE_PAGO' =>
                            array (
                                'CLARO' =>
                                    array (
                                        'QUANTITY' => 86,
                                        'PERCENT' => 98.85,
                                    ),
                                'TIM' =>
                                    array (
                                        'QUANTITY' => 0,
                                        'PERCENT' => 0,
                                    ),
                                'VIVO' =>
                                    array (
                                        'QUANTITY' => 1,
                                        'PERCENT' => 1.15,
                                    ),
                                'TOTAL' =>
                                    array (
                                        'QUANTITY' => 87,
                                        'PERCENT' => 15.7,
                                    ),
                                'DMINUS3' =>
                                    array (
                                        'QUANTITY' => 1,
                                        'PERCENT' => '29/12',
                                    ),
                                'DAY' =>
                                    array (
                                        'QUANTITY' => 3,
                                        'PERCENT' => '02/01',
                                    ),
                            ),
                    ),
                'BODY' =>
                    array (
                        'riachuelo.hierarchy' =>
                            array (
                                'RESUME' =>
                                    array (
                                        'RESUME' =>
                                            array (
                                                'TOTAL' => 554,
                                                'VALUES' =>  24716.760000000006,
                                            ),
                                        'POS_PAGO' =>
                                            array (
                                                'OI' => 36,
                                                'TIM' => 88,
                                                'NEXTEL' => 0,
                                                'VIVO' => 135,
                                                'CLARO' => 208,
                                                'DAY' => 7,
                                                'DMINUS3' => 14,
                                                'TOTAL' => 467,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array (
                                                'CLARO' => 86,
                                                'TIM' => 0,
                                                'VIVO' => 1,
                                                'DAY' => 3,
                                                'DMINUS3' => 1,
                                                'TOTAL' => 87,
                                                'DMINUS' => 0,
                                            ),
                                        'GOALS' =>
                                            array(
                                                'TOTAL' => 78,
                                                'PERCENT' => 710.26,
                                                'GAP' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array (
                                        'riachuelo.pdv' =>
                                            array (
                                                'RESUME' =>
                                                    array (
                                                        'TOTAL' => 554,
                                                        'VALUES' => 24716.760000000006,
                                                    ),
                                                'POS_PAGO' =>
                                                    array (
                                                        'OI' => 36,
                                                        'TIM' => 88,
                                                        'NEXTEL' => 0,
                                                        'VIVO' => 135,
                                                        'CLARO' => 208,
                                                        'DAY' => 7,
                                                        'DMINUS3' => 14,
                                                        'TOTAL' => 467,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array (
                                                        'CLARO' => 86,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'DAY' => 3,
                                                        'DMINUS3' => 1,
                                                        'TOTAL' => 87,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array (
                                                        'TOTAL' => 78,
                                                        'PERCENT' => 710.26,
                                                        'GAP' => 0,
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
            );
    }

    public static function sale()
    {
        return
            array(
                'DATE' => '2018-12-26',
                'TIME' => '15:30',
                'NETWORK' => 'Iplace',
                'HEADERS' =>
                    array(
                        'POS_PAGO' =>
                            array(
                                'OI' =>
                                    array(
                                        'PERCENT' => 0,
                                        'QUANTITY' => 0,
                                    ),
                                'TIM' =>
                                    array(
                                        'PERCENT' => 0,
                                        'QUANTITY' => 0,
                                    ),
                                'VIVO' =>
                                    array(
                                        'PERCENT' => 3.270000000000000017763568394002504646778106689453125,
                                        'QUANTITY' => 150,
                                    ),
                                'CLARO' =>
                                    array(
                                        'PERCENT' => 96.7300000000000039790393202565610408782958984375,
                                        'QUANTITY' => 4436,
                                    ),
                                'NEXTEL' =>
                                    array(
                                        'PERCENT' => 0,
                                        'QUANTITY' => 0,
                                    ),
                                'TOTAL' =>
                                    array(
                                        'PERCENT' => 99.8900000000000005684341886080801486968994140625,
                                        'QUANTITY' => 4586,
                                    ),
                                'DAY' =>
                                    array(
                                        'QUANTITY' => 192,
                                        'PERCENT' => '2018-12-26',
                                    ),
                                'DMINUS' =>
                                    array(
                                        'QUANTITY' => 0,
                                        'PERCENT' => '2018-12-25',
                                    ),
                            ),
                        'PRE_PAGO' =>
                            array(
                                'VIVO' =>
                                    array(
                                        'PERCENT' => 100,
                                        'QUANTITY' => 5,
                                    ),
                                'CLARO' =>
                                    array(
                                        'PERCENT' => 0,
                                        'QUANTITY' => 0,
                                    ),
                                'TOTAL' =>
                                    array(
                                        'PERCENT' => 0.11000000000000000055511151231257827021181583404541015625,
                                        'QUANTITY' => 5,
                                    ),
                                'DAY' =>
                                    array(
                                        'QUANTITY' => 0,
                                        'PERCENT' => '2018-12-26',
                                    ),
                                'DMINUS' =>
                                    array(
                                        'QUANTITY' => 0,
                                        'PERCENT' => '2018-12-25',
                                    ),
                            ),
                        'RESUME' =>
                            array(
                                'TOTAL' =>
                                    array(
                                        'QUANTITY' => 4591,
                                        'PERCENT' => '100%',
                                    ),
                            ),
                    ),
                'BODY' =>
                    array(
                        'Rede iPlace' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 0,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 641' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 0,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Matriz iPlace' =>
                                            array(
                                                'POS_PAGO' =>
                                                    array(
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Território 1' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 5092,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 117,
                                                'CLARO' => 2425,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 5084,
                                                'DAY' => 76,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 4,
                                                'CLARO' => 0,
                                                'TOTAL' => 8,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Regional 1' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 630,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 26,
                                                        'CLARO' => 285,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 622,
                                                        'DAY' => 18,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 4,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 8,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 4' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 1654,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 37,
                                                        'CLARO' => 790,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 1654,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 8' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 266,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 133,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 266,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 10' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 946,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 8,
                                                        'CLARO' => 465,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 946,
                                                        'DAY' => 10,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 11' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 502,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 7,
                                                        'CLARO' => 244,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 502,
                                                        'DAY' => 16,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 12' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 1094,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 39,
                                                        'CLARO' => 508,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 1094,
                                                        'DAY' => 20,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 13' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 0,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Território 2' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 4090,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 33,
                                                'CLARO' => 2011,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 4088,
                                                'DAY' => 116,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 1,
                                                'CLARO' => 0,
                                                'TOTAL' => 2,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Regional 2' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 616,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 4,
                                                        'CLARO' => 304,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 616,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 3' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 780,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 7,
                                                        'CLARO' => 383,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 780,
                                                        'DAY' => 46,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 5' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 910,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 6,
                                                        'CLARO' => 449,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 910,
                                                        'DAY' => 24,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 6 ' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 600,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 298,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 598,
                                                        'DAY' => 16,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 1,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 2,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 7' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 708,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 9,
                                                        'CLARO' => 345,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 708,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                        'Regional 9' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 476,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 6,
                                                        'CLARO' => 232,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 476,
                                                        'DAY' => 14,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 1' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 630,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 26,
                                                'CLARO' => 285,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 622,
                                                'DAY' => 18,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 4,
                                                'CLARO' => 0,
                                                'TOTAL' => 8,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 603' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 118,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 4,
                                                        'CLARO' => 52,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 112,
                                                        'DAY' => 6,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 3,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 6,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 861' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 64,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 3,
                                                        'CLARO' => 29,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 64,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 602' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 50,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 25,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 50,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 610' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 6,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 672' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 66,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 32,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 66,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 815' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 44,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 44,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 825' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 29,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 838' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 66,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 6,
                                                        'CLARO' => 27,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 66,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 853' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 72,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 7,
                                                        'CLARO' => 29,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 72,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 870' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 40,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 3,
                                                        'CLARO' => 16,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 1,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 2,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 878' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 0,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 2,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 2' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 616,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 4,
                                                'CLARO' => 304,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 616,
                                                'DAY' => 8,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 607' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 72,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 36,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 72,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 609' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 146,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 3,
                                                        'CLARO' => 70,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 146,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 647' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 78,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 39,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 78,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 652' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 802' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 68,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 34,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 68,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 821' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 72,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 35,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 72,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 823' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 824' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 44,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 44,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 873' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 40,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 20,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 40,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 3' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 780,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 7,
                                                'CLARO' => 383,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 780,
                                                'DAY' => 46,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 625' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 106,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 53,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 106,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 626' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 4,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 2,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 4,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 635' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 174,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 85,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 174,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 654' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 76,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 38,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 76,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 674' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 40,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 20,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 40,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 676' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 72,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 35,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 72,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 804' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 56,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 28,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 56,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 812' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 4,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 2,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 4,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 826' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 34,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 17,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 34,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 848' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 72,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 34,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 72,
                                                        'DAY' => 22,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 857' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 22,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 10,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 22,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 867' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 88,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 43,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 88,
                                                        'DAY' => 6,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 882' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 32,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 16,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 32,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 4' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 1654,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 37,
                                                'CLARO' => 790,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 1654,
                                                'DAY' => 8,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 884' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 68,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 8,
                                                        'CLARO' => 26,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 68,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 634' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 162,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 11,
                                                        'CLARO' => 70,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 162,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 636' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 64,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 32,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 64,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 637' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 152,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 74,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 152,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 638' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 172,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 86,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 172,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 650' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 70,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 35,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 70,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 660' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 90,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 45,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 90,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 661' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 78,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 39,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 78,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 664' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 38,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 19,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 670' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 50,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 23,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 50,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 673' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 30,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 15,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 30,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 818' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 148,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 7,
                                                        'CLARO' => 67,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 148,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 819' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 106,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 51,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 106,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 831' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 90,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 44,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 90,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 832' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 108,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 53,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 108,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 835' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 142,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 70,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 142,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 874' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 86,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 41,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 86,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 5' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 910,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 6,
                                                'CLARO' => 449,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 910,
                                                'DAY' => 24,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 629' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 32,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 16,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 32,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 617' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 84,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 41,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 84,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 622' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 29,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 623' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 124,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 61,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 124,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 627' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 112,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 56,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 112,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 813' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 62,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 31,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 62,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 814' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 62,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 30,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 62,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 817' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 46,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 46,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 849' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 50,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 25,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 50,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 854' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 158,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 78,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 158,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 862' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 90,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 45,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 90,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 881' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 30,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 15,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 30,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 6 ' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 600,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 1,
                                                'CLARO' => 298,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 598,
                                                'DAY' => 16,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 1,
                                                'CLARO' => 0,
                                                'TOTAL' => 2,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 624' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 30,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 15,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 30,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 806' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 84,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 42,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 84,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 615' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 84,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 41,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 82,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 1,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 2,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 620' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 56,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 28,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 56,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 630' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 22,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 11,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 22,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 651' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 23,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 655' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 34,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 17,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 34,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 810' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 18,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 9,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 18,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 811' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 30,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 863' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 70,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 35,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 70,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 872' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 38,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 19,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 876' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 56,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 28,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 56,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 7' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 708,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 9,
                                                'CLARO' => 345,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 708,
                                                'DAY' => 8,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 639' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 142,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 71,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 142,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 646' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 36,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 17,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 36,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 648' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 66,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 33,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 66,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 653' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 38,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 19,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 658' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 665' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 68,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 34,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 68,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 829' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 74,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 37,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 74,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 830' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 40,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 20,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 40,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 836' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 26,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 6,
                                                        'CLARO' => 7,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 26,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 839' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 78,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 39,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 78,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 846' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 52,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 52,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 866' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 26,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 13,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 26,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 869' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 14,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 7,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 14,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 8' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 266,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 0,
                                                'CLARO' => 133,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 266,
                                                'DAY' => 4,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 649' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 66,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 33,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 66,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 833' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 98,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 49,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 98,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 834' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 22,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 11,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 22,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 837' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 16,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 8,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 16,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 859' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 40,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 20,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 40,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 860' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 24,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 12,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 24,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 9' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 476,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 6,
                                                'CLARO' => 232,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 476,
                                                'DAY' => 14,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 880' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 22,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 11,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 22,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 801' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 46,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 23,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 46,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 803' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 30,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 805' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 42,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 21,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 42,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 807' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 44,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 44,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 809' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 26,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 13,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 26,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 816' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 54,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 27,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 54,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 822' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 36,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 18,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 36,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 843' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 50,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 24,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 50,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 845' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 8,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 4,
                                                        'CLARO' => 0,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 8,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 847' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 22,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 11,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 22,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 855' =>
                                            array(
                                                'POS_PAGO' =>
                                                    array(
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 856' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 66,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 32,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 66,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 10' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 946,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 8,
                                                'CLARO' => 465,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 946,
                                                'DAY' => 10,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 606' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 44,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 44,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 608' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 168,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 84,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 168,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 618' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 128,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 62,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 128,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 631' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 30,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 632' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 172,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 84,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 172,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 662' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 110,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 55,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 110,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 668' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 60,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 28,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 60,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 675' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 48,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 48,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 864' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 62,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 31,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 62,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 865' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 56,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 28,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 56,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 879' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 38,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 19,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 11' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 502,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 7,
                                                'CLARO' => 244,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 502,
                                                'DAY' => 16,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 605' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 44,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 44,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 666' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 84,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 41,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 84,
                                                        'DAY' => 6,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 667' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 52,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 26,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 52,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 820' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 126,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 62,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 126,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 840' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 38,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 4,
                                                        'CLARO' => 15,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 38,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 841' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 46,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 22,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 46,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 842' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 78,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 39,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 78,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 844' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 34,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 17,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 34,
                                                        'DAY' => 6,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 12' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 1094,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 39,
                                                'CLARO' => 508,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 1094,
                                                'DAY' => 20,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(
                                        'Iplace - 633' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 124,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 9,
                                                        'CLARO' => 53,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 124,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 656' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 82,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 41,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 82,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 657' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 42,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 19,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 42,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 659' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 82,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 6,
                                                        'CLARO' => 35,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 82,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 669' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 54,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 7,
                                                        'CLARO' => 20,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 54,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 671' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 104,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 51,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 104,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 677' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 100,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 10,
                                                        'CLARO' => 40,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 100,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 678' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 64,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 32,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 64,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 827' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 70,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 34,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 70,
                                                        'DAY' => 2,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 828' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 116,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 2,
                                                        'CLARO' => 56,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 116,
                                                        'DAY' => 8,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 858' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 70,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 1,
                                                        'CLARO' => 34,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 70,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 868' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 50,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 25,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 50,
                                                        'DAY' => 4,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 875' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 62,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 31,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 62,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                        'Iplace - 877' =>
                                            array(
                                                'RESUME' =>
                                                    array(
                                                        'TOTAL' => 74,
                                                    ),
                                                'POS_PAGO' =>
                                                    array(
                                                        'OI' => 0,
                                                        'TIM' => 0,
                                                        'VIVO' => 0,
                                                        'CLARO' => 37,
                                                        'NEXTEL' => 0,
                                                        'TOTAL' => 74,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'PRE_PAGO' =>
                                                    array(
                                                        'VIVO' => 0,
                                                        'CLARO' => 0,
                                                        'TOTAL' => 0,
                                                        'DAY' => 0,
                                                        'DMINUS' => 0,
                                                    ),
                                                'GOALS' =>
                                                    array(
                                                        'TOTAL' => 2,
                                                        'DONE' => 3,
                                                        'GAP' => 4,
                                                    ),
                                            ),
                                    ),
                            ),
                        'Regional 13' =>
                            array(
                                'RESUME' =>
                                    array(
                                        'RESUME' =>
                                            array(
                                                'TOTAL' => 0,
                                            ),
                                        'POS_PAGO' =>
                                            array(
                                                'OI' => 0,
                                                'TIM' => 0,
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'NEXTEL' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                        'PRE_PAGO' =>
                                            array(
                                                'VIVO' => 0,
                                                'CLARO' => 0,
                                                'TOTAL' => 0,
                                                'DAY' => 0,
                                                'DMINUS' => 0,
                                            ),
                                    ),
                                'DETAILS' =>
                                    array(),
                            ),
                    ),
            );
    }
}

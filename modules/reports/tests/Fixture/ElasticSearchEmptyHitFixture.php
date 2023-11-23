<?php

namespace Reports\Tests\Fixture;

class ElasticSearchEmptyHitFixture
{
    public static function getSaleArray()
    {
        return [
            'took' => 5,
            'timed_out' => false,
            '_shards' =>
                [
                    'total' => 5,
                    'successful' => 5,
                    'skipped' => 0,
                    'failed' => 0,
                ],
            'hits' =>
                [
                    'total' => 1,
                    'max_score' => 1,
                    'hits' => null
                ]
        ];
    }
}

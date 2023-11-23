<?php

namespace Recommendation\tests;

final class RecommendationTestBook
{
    public const SUCCESS_REGISTRATION = '1502552';
    public const INVALID_REGISTRATION = '999999';
    public const VALID_RECOMMENDATION = [
        'name' => 'Example',
        'statusCode' => 'ACTIVE',
        'registration' => self::SUCCESS_REGISTRATION,
        'pointOfSaleId' => 0,
    ];
}

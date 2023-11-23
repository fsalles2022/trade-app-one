<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Feature;

use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ReportExternalSaleFeatureTest extends TestCase
{
    use AuthHelper;

    public const URI = 'reports/analytical_report/external_sales';

    /** @test */
    public function export_report_successfully(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', self::URI, self::getFiltersSuccess());

        $response->assertStatus(200);
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function report_export_without_sales(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', self::URI, self::getFilterFailure());

        $response->assertStatus(200);
        $this->assertNotEmpty($response->getContent());
    }

    /** @return mixed[] */
    private static function getFiltersSuccess(): array
    {
        return array_merge(Siv3TestBook::REPORT_EXTERNAL_SALE_FILTERS_SUCCESS, Siv3TestBook::DATE_SUCESS_EXPORT);
    }

    /** @return mixed[] */
    private static function getFilterFailure(): array
    {
        return array_merge(Siv3TestBook::REPORT_EXTERNAL_SALE_FILTERS_FAILURE, Siv3TestBook::DATE_FAILURE_EXPORT);
    }
}

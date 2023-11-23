<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3\Siv3Tests\Unit\Adapters;

use ClaroBR\Reports\Adapters\ExternalSalesMap;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use TradeAppOne\Tests\TestCase;
use ClaroBR\Reports\Headers\ExternalSalesCsvHeaders;

class ExternalSalesMapTest extends TestCase
{
    /** @test */
    public function map_should_return_only_one_sale_with_length_equal_to_eight_with_filled_sale(): void
    {
        $array = Siv3TestBook::SALES_EXPORTABLE['data'];
        
        $mappedArray = ExternalSalesMap::recordsToArray($array)[1];

        $arrayLength = count($mappedArray);
        
        $this->assertEquals(16, $arrayLength);
    }
    
    /** @test */
    public function map_should_return_only_one_sale_with_length_equal_to_eight_with_empty_sale(): void
    {
        $array = Siv3TestBook::NON_EXISTENTS_SALES_EXPORTABLE['data'];

        $mappedArray = ExternalSalesMap::recordsToArray($array);

        $arrayLength = count($mappedArray);

        $this->assertEquals(1, $arrayLength);
    }

    /** @test */
    public function map_should_return_the_correct_header(): void
    {
        $header = array_merge(...ExternalSalesCsvHeaders::headings());

        $array = Siv3TestBook::SALES_EXPORTABLE['data'];
        
        $mappedArray = ExternalSalesMap::recordsToArray($array)[0];

        $this->assertEquals($header, $mappedArray);
    }
}

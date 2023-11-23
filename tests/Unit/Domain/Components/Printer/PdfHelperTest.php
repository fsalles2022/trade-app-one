<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Printer;

use TradeAppOne\Domain\Components\Printer\PdfHelper;
use TradeAppOne\Tests\TestCase;

class PdfHelperTest extends TestCase
{
    /** @test */
    public function should_call_from_html_return_pdf_content()
    {
        $html = '<h1> Hello </h1>';

        $result = (new PdfHelper())->fromHtmlToContent($html, []);

        $this->assertInternalType('string', $result);
    }

    /** @test */
    public function should_return_an_instance()
    {
        $class = new PdfHelper();

        $className = get_class($class);

        $this->assertEquals(PdfHelper::class, $className);
    }
}

<?php

namespace Outsourced\Cea\tests\Feature\Importables;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class CeaGiftCardsImportableTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_importable_all_gift_cards()
    {
        $fileName = 'giftCards.csv';
        $file     = "codes,partner\n1234,TRIANGULATION\n4321,TRADE_IN";

        Storage::fake('files');
        Storage::disk('files')->put($fileName, $file);
        $path = Storage::disk('files')->path($fileName);

        $csv = (new UploadedFile($path, $fileName, null, null, null, true));

        $this->authAs()->post('outsourced/cea/import-gift-cards', ['file' => $csv]);

        $this->assertDatabaseHas('cea_gift_cards', ['code' => '1234'], 'outsourced');
        $this->assertDatabaseHas('cea_gift_cards', ['code' => '4321'], 'outsourced');
    }

    /** @test */
    public function get_should_return_csv_example_cea_importable()
    {
        $response = $this->authAs()->get('outsourced/cea/import-gift-cards');

        $content = $response->getContent();

        $expectedResponse = "codes;partner\n123456;TRIANGULATION\n654321;TRADE_IN\n";

        $this->assertEquals($expectedResponse, $content);
    }
}

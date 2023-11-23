<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\ImportableInterface;
use TradeAppOne\Domain\Importables\ImportEngine;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImportEngineTest extends TestCase
{
    protected $file;
    protected $filePointer;
    protected $importable;

    /** @test */
    public function process_should_return_mapped_records_when_csv_is_valid()
    {
        $engine         = new ImportEngine($this->importable);
        $resultAfterMap = str_getcsv($engine->process($this->file), ';');
        self::assertNotEmpty($resultAfterMap);
    }

    /** @test */
    public function return_null_errors_when_file_imported_successfully()
    {
        $engine         = new ImportEngine($this->importable);
        $resultAfterMap = str_getcsv($engine->process($this->file), ';');
        self::assertNull($resultAfterMap[0]);
    }

    /** @test */
    public function init_file_should_return_headers_when_csv_sent()
    {
        $expected = ['column1', 'column2'];

        $method = self::getMethod('initFile');
        $engine = new ImportEngine($this->importable);
        self::assertEquals($expected, $method->invokeArgs($engine, [$this->file])[0]);
    }

    protected static function getMethod($name)
    {
        $class  = new ReflectionClass(ImportEngine::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /** @test */
    public function validate_should_return_true_when_valid_csv_sent()
    {
        $engine   = new ImportEngine($this->importable);
        $initFile = $method = self::getMethod('initFile');
        $headers  = $initFile->invokeArgs($engine, [$this->file])[0];
        $method   = self::getMethod('validateFile');
        self::assertTrue($method->invokeArgs($engine, [$headers]));
    }

    /** @test */
    public function should_return_headers_of_importable_instance()
    {
        $engine   = new ImportEngine($this->importable);
        $initFile = $method = self::getMethod('initFile');
        $records  = $initFile->invokeArgs($engine, [$this->file]);
        self::assertNotEmpty($records[1]);
    }

    protected function setUp()
    {
        parent::setUp();

        Storage::fake('s3');

        $this->importable = $this->getMockBuilder(ImportableInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['processLine', 'getColumns', 'getExample', 'getType'])
            ->getMock();
        $this->importable->method('getExample')->will($this->returnValue(null));
        $this->importable->method('processLine')->will($this->returnValue(null));
        $this->importable->method('getType')->will($this->returnValue('TYPE-IMPORTATION'));
        $this->importable
            ->method('getColumns')
            ->will($this->returnValue(['column1', 'column2']));

        $this->filePointer = fopen(__DIR__ . '/testUnitCsv.csv', 'w');

        $content = CsvHelper::arrayToCsv([0 => ['column1', 'column2'], 1 => ['line1', 'line2']]);
        fwrite($this->filePointer, $content);
        $this->file = tap(
            new File('ade', $this->filePointer),
            function ($file) {
                $file->sizeToReport = 2 * 1024;
            }
        );

        $userImportation = (new UserBuilder())->build();
        $this->be($userImportation);
    }

    public function tearDown()
    {
        fclose($this->filePointer);
        parent::tearDown();
    }
}

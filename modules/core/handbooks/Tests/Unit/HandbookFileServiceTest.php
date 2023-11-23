<?php

namespace Core\HandBooks\Tests\Unit;

use Core\HandBooks\Http\Requests\HandbookFormRequest;
use Core\HandBooks\Models\HandbookRequest;
use Core\HandBooks\Services\HandbookFileService;
use Core\HandBooks\Tests\Helpers\File\HandbookFileTestHelper;
use Core\HandBooks\Tests\Helpers\HandbookBuilder;
use TradeAppOne\Domain\Enumerators\Files\FileExtensions;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Facades\S3;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\TestCase;

class HandbookFileServiceTest extends TestCase
{
    /** @test */
    public function should_call_create_when_handbook_is_null()
    {
        $mock = \Mockery::mock(HandbookFileService::class)->makePartial();
        $mock->shouldReceive('create')->once()->andReturn('');
        $mock->shouldReceive('update')->never();

        $mock->save($this->mockRequest());
    }

    /** @test */
    public function should_call_update_when_isset_handbook()
    {
        $mock = \Mockery::mock(HandbookFileService::class)->makePartial();
        $mock->shouldReceive('create')->never();
        $mock->shouldReceive('update')->once()->andReturn('');

        $handbook = (new HandbookBuilder())->build();

        $mock->save($this->mockRequest(), $handbook);
    }

    /** @test */
    public function should_call_s3_and_return_path_to_create()
    {
        $data = [
            'module' => Operations::SECURITY,
            'title' =>  'manual-mcfee',
            'file' => HandbookFileTestHelper::file()
        ];

        S3::shouldReceive('put')->once()->andReturn('');
        Uniqid::shouldReceive('generate')->andReturn('1234');

        $create = $this->service()->create($this->mockRequest($data));

        $expectedPath = HandbookFileService::generatePath(Operations::SECURITY, 'manual-mcfee', FileExtensions::PDF);
        $this->assertEquals($expectedPath, $create);
    }

    /** @test */
    public function should_return_empty_string_when_file_not_exists_in_update()
    {
        S3::shouldReceive('delete')->never();
        S3::shouldReceive('put')->never();

        $handbook = (new HandbookBuilder())->build();
        $create   = (new HandbookFileService())->update($this->mockRequest(), $handbook);

        $this->assertEquals('', $create);
    }

    /** @test */
    public function should_call_s3_and_return_path_to_update()
    {
        $data = [
            'module' => Operations::SECURITY,
            'title' =>  'manual-mcfee',
            'file' => HandbookFileTestHelper::file()
        ];

        $handbook = (new HandbookBuilder())->build();

        S3::shouldReceive('delete')->once()->andReturn('');
        S3::shouldReceive('put')->once()->andReturn('');
        Uniqid::shouldReceive('generate')->andReturn('1234');

        $create = $this->service()->update($this->mockRequest($data), $handbook);

        $expectedPath = HandbookFileService::generatePath($handbook->module, 'manual-mcfee', FileExtensions::PDF);
        $this->assertEquals($expectedPath, $create);
    }

    /** @test */
    public function should_update_file_with_title_handbook_when_not_exists_in_data()
    {
        $data = [
            'module' => Operations::SECURITY,
            'file' => HandbookFileTestHelper::file()
        ];

        $handbook = (new HandbookBuilder())->build();

        S3::shouldReceive('delete')->once()->andReturn('');
        S3::shouldReceive('put')->once()->andReturn('');
        Uniqid::shouldReceive('generate')->andReturn('1234');

        $create = $this->service()->update($this->mockRequest($data), $handbook);

        $expectedPath = HandbookFileService::generatePath($handbook->module, $handbook->title, FileExtensions::PDF);
        $this->assertEquals($expectedPath, $create);
    }

    /** @test */
    public function should_generate_correct_path()
    {
        Uniqid::shouldReceive('generate')->andReturn('1234');

        $path   = HandbookFileService::generatePath('module', 'title', 'mp4');
        $expect = HandbookFileService::DEFAULT_DIR . '/module/mp4/1234-title.mp4';

        $this->assertEquals($expect, $path);
    }

    private function mockRequest(array $data = []): HandbookRequest
    {
        $mockRequest = \Mockery::mock(HandbookFormRequest::class)->makePartial();
        $mockRequest->shouldReceive('validated')->andReturn($data);
        $mockRequest->shouldReceive('user')->andReturnNull();

        return new HandbookRequest($mockRequest);
    }

    private function service(): HandbookFileService
    {
        return resolve(HandbookFileService::class);
    }
}

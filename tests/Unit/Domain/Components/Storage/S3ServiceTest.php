<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Components\Storage\S3Service;
use TradeAppOne\Exceptions\SystemExceptions\S3Exceptions;
use TradeAppOne\Tests\TestCase;

class S3ServiceTest extends TestCase
{
    /** @test */
    public function should_return_path_when_success_put()
    {
        $storage = \Mockery::mock(Filesystem::class);
        $storage->shouldReceive('put')
            ->once()
            ->andReturnTrue();

        $this->mockDisk($storage);

        $received = S3Service::put('', '');
        $this->assertTrue($received);
    }

    /** @test */
    public function should_return_exception_when_storage_return_false_in_put()
    {
        $storage = \Mockery::mock(Filesystem::class);
        $storage->shouldReceive('put')
            ->once()
            ->andReturnFalse();

        $this->mockDisk($storage);

        $this->expectExceptionMessage(trans('exceptions.s3.' . S3Exceptions::PUT_ERROR));
        S3Service::put('', '');
    }

    /** @test */
    public function should_return_exception_when_storage_return_exception_in_put()
    {
        $storage = \Mockery::mock(Filesystem::class);

        $this->mockDisk($storage);

        $this->expectExceptionMessage(trans('exceptions.s3.' . S3Exceptions::PUT_ERROR));
        S3Service::put('', '');
    }

    /** @test */
    public function should_return_true_when_success_delete()
    {
        $storage = \Mockery::mock(Filesystem::class);
        $storage->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $this->mockDisk($storage);

        $received = S3Service::delete('');
        $this->assertTrue($received);
    }

    /** @test */
    public function should_return_exception_when_storage_return_false_in_delete()
    {
        $storage = \Mockery::mock(Filesystem::class);
        $storage->shouldReceive('delete')
            ->once()
            ->andReturnFalse();

        $this->mockDisk($storage);

        $this->expectExceptionMessage(trans('exceptions.s3.' . S3Exceptions::DELETE_ERROR));
        S3Service::delete('');
    }

    /** @test */
    public function should_return_exception_when_storage_return_exception_in_delete()
    {
        $storage = \Mockery::mock(Filesystem::class);

        $this->mockDisk($storage);

        $this->expectExceptionMessage(trans('exceptions.s3.' . S3Exceptions::DELETE_ERROR));
        S3Service::delete('', '');
    }

    /** @test */
    public function should_return_instance_when_success_download()
    {
        $storage = \Mockery::mock(Filesystem::class);
        $storage->shouldReceive('download')
            ->once()
            ->andReturn(new StreamedResponse());

        $this->mockDisk($storage);

        S3Service::download('');
    }

    /** @test */
    public function should_return_exception_when_storage_return_exception_in_download()
    {
        $storage = \Mockery::mock(Filesystem::class);

        $this->mockDisk($storage);

        $this->expectExceptionMessage(trans('exceptions.s3.' . S3Exceptions::DOWNLOAD_ERROR));
        S3Service::download('', '');
    }

    private function mockDisk($storage)
    {
        Storage::fake(S3Service::STORAGE);
        Storage::shouldReceive('disk')
            ->with(S3Service::STORAGE)
            ->once()
            ->andReturn($storage);
    }

    private function service(): S3Service
    {
        return resolve(S3Service::class);
    }
}
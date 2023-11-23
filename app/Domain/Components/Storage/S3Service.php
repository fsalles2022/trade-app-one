<?php

namespace TradeAppOne\Domain\Components\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Exceptions\SystemExceptions\S3Exceptions;
use Symfony\Component\HttpFoundation\StreamedResponse;

class S3Service
{
    public const STORAGE = 's3';

    public static function put(string $path, $file, $options = []): bool
    {
        try {
            $status = self::disk()->put($path, $file, $options);
        } catch (\Exception $exception) {
            throw S3Exceptions::errorPut($exception->getMessage());
        }

        if ($status === false) {
            throw S3Exceptions::errorPut(trans('exceptions.s3.status_false'));
        }

        return true;
    }

    public static function delete(string $filePath): bool
    {
        try {
            $status = self::disk()->delete($filePath);
        } catch (\Exception $exception) {
            throw S3Exceptions::errorDelete($exception->getMessage());
        }

        if ($status === false) {
            throw S3Exceptions::errorDelete(trans('exceptions.s3.status_false'));
        }

        return true;
    }

    public static function download(string $filePath): StreamedResponse
    {
        try {
            return self::disk()->download($filePath);
        } catch (\Exception $exception) {
            throw S3Exceptions::errorDownload($exception->getMessage());
        }
    }

    public static function url(string $filePath): string
    {
        return self::disk()->url($filePath);
    }

    private static function disk(): Filesystem
    {
        try {
            return Storage::disk(self::STORAGE);
        } catch (\Exception $exception) {
            throw S3Exceptions::errorConfig($exception->getMessage());
        }
    }
}

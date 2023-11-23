<?php

declare(strict_types=1);

namespace Bulletin\Service;

use Bulletin\Enumerators\BulletinFileServerEnumerator;
use Bulletin\Models\Bulletin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Facades\S3;

class BulletinFileServer
{
    /**
     * @param Network $network
     * @param UploadedFile $file
     * @param string $fileName
     * @return string
     */
    public static function save(Network $network, UploadedFile $file, string $fileName): string
    {
        return self::awsStore($network, $file, $fileName);
    }

    /**
     * @param Bulletin $bulletin
     * @param UploadedFile $file
     * @param string $fileName
     * @return string
     */
    public static function update(Bulletin $bulletin, UploadedFile $file, string $fileName): string
    {
        self::delete($bulletin);
        return self::awsStore($bulletin->network, $file, $fileName);
    }

    /**
     * @param Bulletin $bulletin
     * @return bool
     */
    public static function delete(Bulletin $bulletin): bool
    {
        $urlImage = $bulletin->urlImage;
        $realPath = filter_var($urlImage, FILTER_VALIDATE_URL)
            ? substr($urlImage, strpos($urlImage, '/' . BulletinFileServerEnumerator::DEFAULT_AWS_PATH))
            : $urlImage;

        return S3::delete($realPath);
    }

    /**
     * @param string $networkSlug
     * @param string $fileName
     * @return string
     */
    private static function path(string $networkSlug, string $fileName): string
    {
        $slug     = strtolower($networkSlug);
        $date     = Carbon::now();
        $date->tz = config('app.timezone');
        $year     = $date->format('Y');
        $month    = $date->format('m');
        $day      = $date->format('d');

        return BulletinFileServerEnumerator::DEFAULT_AWS_PATH . "/{$slug}/{$year}/{$month}/{$day}/$fileName";
    }

    /**
     * @param UploadedFile $file
     * @return mixed
     */
    private static function adapterImage(UploadedFile $file)
    {
        $image = Image::make($file->getRealPath());
        $image->encode($file->extension());

        return $image;
    }

    /**
     * @param array $attributes
     * @return string
     */
    public static function getFileName(array $attributes): string
    {
        $sufix = \Carbon\Carbon::now()->getTimestamp();
        $name  = str_slug(($attributes['title'] ?? ''));

        return "{$sufix}-{$name}";
    }

    /**
     * @param Network $network
     * @param string $fileName
     * @param UploadedFile $file
     * @return string
     */
    private static function awsStore(Network $network, UploadedFile $file, string $fileName): string
    {
        $path = self::path($network->slug, "{$fileName}.{$file->extension()}");
        S3::put($path, self::adapterImage($file), 'public');

        return $path;
    }
}

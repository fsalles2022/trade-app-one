<?php

declare(strict_types=1);

namespace Bulletin\tests\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class Functions
{
    /**
     * @param string $networkSlug
     * @param string $pointOfSaleSlug
     * @param string $roleSlug
     * @return Mixed[]
     */
    public static function payloadStoreBulletin(string $networkSlug, string $pointOfSaleSlug, string $roleSlug): array
    {
        $date = Carbon::now();

        $payload = [
            'networks' => [
                [
                    'network' => [
                        'slug' => [$networkSlug],
                    ],
                    'pointOfSales' => [
                        ['slug' => $pointOfSaleSlug]
                    ],
                    'roles' => [
                        ['slug' => $roleSlug]
                    ]
                ]
            ],
            'title' => 'New Promotion',
            'description' => "New Sales",
            'period' => [
                'startDate' => $date->format('Y-m-d'),
                'endDate' => $date->addMonth()->format('Y-m-d')
            ],
            'status' => true
        ];
        return $payload;
    }

    /** @return Mixed[] */
    public static function makeTestImage(): array
    {
        $image = imagecreate(150, 150);
        imagecolorallocate($image, 0, 153, 0);

        $path = __DIR__ . '/test-save.png';

        imagepng($image, $path);

        $uploadedFile = new UploadedFile(
            $path,
            'test-save.png',
            'image/png',
            null,
            null,
            true
        );

        return compact('uploadedFile', 'path');
    }
}

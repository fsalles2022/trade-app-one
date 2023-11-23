<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use Psr\Http\Message\RequestInterface;

trait RequestUtils
{
    /** @return mixed[] */
    protected function deserializeRequestBody(RequestInterface $request): array
    {
        $json = $request->getBody()->getContents();

        return json_decode($json, true);
    }

    protected function getResponseJsonByFilePath(string $filePath): string
    {
        if (file_exists($filePath) === false) {
            return '{}';
        }

        $json = file_get_contents($filePath);

        return $json === false ? '{}' : $json;
    }
}

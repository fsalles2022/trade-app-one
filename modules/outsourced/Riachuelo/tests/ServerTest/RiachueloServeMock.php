<?php

namespace Outsourced\Riachuelo\tests\ServerTest;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use Outsourced\Riachuelo\Connections\Authentication\AuthenticationRoutes;
use Outsourced\Riachuelo\Connections\RiachueloRoutes;
use Outsourced\Riachuelo\tests\RiachueloEnumTest;
use Psr\Http\Message\RequestInterface;

class RiachueloServeMock
{
    public function __invoke(RequestInterface $request, array $options)
    {
        $path = $request->getUri()->getPath();

        $device = file_get_contents(__DIR__ . '/responses/devices/device.json');
        $token  = file_get_contents(__DIR__ . '/responses/authentication/access_token.json');

        switch ($path) {
            case AuthenticationRoutes::AUTH:
                $body = stream_for($token);
                break;
            case RiachueloRoutes::deviceByImei(RiachueloEnumTest::DEVICE_IMEI):
                $body = stream_for($device);
                break;
            default:
                $body = stream_for('{}');
        }

        return new FulfilledPromise(
            new Response(200, ['Content­Type' => 'application/json'], $body)
        );
    }
}

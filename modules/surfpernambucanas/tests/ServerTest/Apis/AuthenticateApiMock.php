<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\ServerTest\Apis;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class AuthenticateApiMock implements PagtelApiMockInterface
{
    /** @inheritDoc */
    public static function make(): self
    {
        return new self();
    }

    /** @inheritDoc */
    public function getMock(RequestInterface $request): Response
    {
        return new Response(
            200,
            ['Content­Type' => 'application/json'],
            $this->sucessBody()
        );
    }

    protected function sucessBody(): string
    {
        return json_encode([
            'authenticated' => true,
            'created'       => '2019-07-09 13:26:40',
            'expiration'    => '2019-07-09 14:26:40',
            'accessToken'   => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdlcnNvbiIsImp0aSI6IjY1OGJlNjM0ZDhlZjQ2NDU4NmY4MzMxOWQ3ZTllNzE3IiwiYWNyIjoiR2Vyc29uIiwibmJmIjoxNTYyNjg5NjAwLCJleHAiOjE1NjI2OTMyMDAsImlhdCI6MTU2MjY4OTYwMCwiaXNzIjoiSHViMzYwQVBJIiwiYXVkIjoiQXVkaWVuY2VfSFVCMzYwIn0.DnWpvEttROoLwu2UQcPnXIN5WpRGhdTnf7wG_XnWXYpsEmGCRFxbJh3zqYqm-SgChGxY0qJDtVOtmY1dO1vwp1_BVX4MIex1GDJ1EFGS3Dspne_fhh6uKaLWbTw4WJ4ml0Xw4QEnh587s6KcbvJOiIXzddneQaOTSXQBb2w8Z7ZZskmmfHXvKYMcpmWh2Ec7Gd_tCadACyqlhRAnom5cdxcBzlRY3NMKDO5NzjeA1-U5docujIxZP0wSY3KqDDMGBVNeXebPYLR7vGAEW7VR8ZlOt0_9Q3qkZZteqXDCjiQb0W4-tUJlDhrkb_uff_VNkU8ihnJQgkK2CGDmrQ',
            'refreshToken'  => 'b3f57f07313c4de3af25058fadbae2a8',
            'message'       => 'OK',
        ]);
    }
}

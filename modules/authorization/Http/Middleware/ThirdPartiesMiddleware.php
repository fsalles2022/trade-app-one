<?php

namespace Authorization\Http\Middleware;

use Authorization\Models\ThirdPartyClient;
use Authorization\Services\ThirdPartiesAccessFactory;
use Closure;
use Illuminate\Http\Request;

class ThirdPartiesMiddleware
{
    public const ACCESS_KEY = 'accessKey';

    protected $thirdPartyFactory;
    private $request;

    public function __construct(ThirdPartiesAccessFactory $thirdPartiesAccess)
    {
        $this->thirdPartyFactory = $thirdPartiesAccess;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->request = $request;

        if ($thirdPartyClient = $this->getThirdPartyWhenPresent()) {
            $bearerToken = $thirdPartyClient
                ->withIp($request->ip())
                ->withRoute($request->path(), $request->method())
                ->retriveBearerToken();

            $request->headers->set('Authorization', $bearerToken);
        }

        return $next($request);
    }

    private function getThirdPartyWhenPresent(): ?ThirdPartyClient
    {
        $accessKeyHeader = $this->request->header(self::ACCESS_KEY);

        $isThirdPartyClient = filled($accessKeyHeader);

        if ($isThirdPartyClient) {
            return $this->thirdPartyFactory->getByAccessKey($accessKeyHeader);
        }

        return null;
    }
}

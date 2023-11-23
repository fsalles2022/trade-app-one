<?php

namespace TradeAppOne\Domain\Adapters;

use Illuminate\Http\Response;
use TradeAppOne\Domain\HttpClients\Responseable;

class ResponseAdapterAbstract
{
    /**
     * @var Responseable
     */
    protected $originalResponse;
    protected $status               = Response::HTTP_OK;
    protected $defaultSuccessStatus = Response::HTTP_OK;
    protected $defaultErrorStatus   = Response::HTTP_PRECONDITION_FAILED;
    protected $adapted              = [];

    public function __construct(Responseable $originalResponse)
    {
        $this->originalResponse = $originalResponse;
    }

    public function getOriginal(): array
    {
        return $this->originalResponse->toArray();
    }

    public function adapt()
    {
        return \response()->json($this->getAdapted(), $this->getStatus());
    }

    public function getAdapted(): array
    {
        return $this->adapted;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function pushAttributes(array $attributes = [])
    {
        $this->adapted = array_merge($this->adapted, $attributes);
    }

    public function __toString()
    {
        return $this->adapted;
    }

    public function isSuccess(): bool
    {
        return $this->response->isSuccess();
    }

    protected function pushError(array $content = [], $status = Response::HTTP_PRECONDITION_FAILED)
    {
        $this->status            = $status;
        $this->adapted['errors'] = [];
        array_push($this->adapted['errors'], $content);
        return $this->adapted;
    }
}

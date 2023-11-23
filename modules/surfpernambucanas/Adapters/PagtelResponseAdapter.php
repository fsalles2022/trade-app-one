<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

use Illuminate\Http\Response;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class PagtelResponseAdapter extends ResponseAdapterAbstract
{
    public function __construct(Responseable $originalResponse)
    {
        parent::__construct($originalResponse);

        $this->adapted = $this->adaptData();
        $this->status  = $this->getStatusCode();
    }

    /** @return mixed[] */
    protected function adaptData(): array
    {
        return [
            'code' => $this->originalResponse->get('code'),
            'msg'  => $this->getMessageResponse(),
        ];
    }

    protected function getMessageResponse(): string
    {
        $message     = $this->originalResponse->get('msg');
        $description = $this->originalResponse->get('description');

        return $message ?? $description ?? '';
    }

    /**
     * Especify here others status codes that represents success
     *
     * @return string[]
     */
    protected function getOthersSuccessCodes(): array
    {
        return [
            PagtelResponseCode::PORTIN_SUCCESS,
        ];
    }

    protected function isSuccessOtherCodeByCode(?string $code): bool
    {
        return in_array($code, $this->getOthersSuccessCodes(), true);
    }

    public function isSuccess(): bool
    {
        $code = $this->originalResponse->get('code');

        return $code === PagtelResponseCode::SUCCESS ?: $this->isSuccessOtherCodeByCode($code) ?: false;
    }

    protected function getStatusCode(): int
    {
        return $this->isSuccess() ? $this->defaultSuccessStatus : Response::HTTP_CONFLICT;
    }

    public function __toString(): string
    {
        return json_encode($this->adapted);
    }
}

<?php

declare(strict_types=1);

namespace TimBR\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetAuthenticateStatusResource extends Resource
{
    /** @inheritDoc */
    public function toArray($request): array
    {
        return [
            'authenticate' => $this->resource['authenticate'] ?? null,
            'status' => $this->resource['status'] ?? null,
        ];
    }
}

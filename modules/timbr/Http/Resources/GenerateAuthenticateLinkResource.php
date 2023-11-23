<?php

declare(strict_types=1);

namespace TimBR\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GenerateAuthenticateLinkResource extends Resource
{
    /** @inheritDoc */
    public function toArray($request): array
    {
        return [
            'authenticateLink' => $this->resource['link'] ?? null,
            'authenticateLinkId' => $this->resource['linkId'] ?? null,
        ];
    }
}

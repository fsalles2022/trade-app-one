<?php

declare(strict_types=1);

namespace TimBR\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GetSaleTermStatusResource extends Resource
{
    /** @inheritDoc */
    public function toArray($request): array
    {
        return [
            'saleTerm' => $this->resource['saleTerm'] ?? null,
            'status' => $this->resource['status'] ?? null,
        ];
    }
}

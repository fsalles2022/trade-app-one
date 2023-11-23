<?php

declare(strict_types=1);

namespace TimBR\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GenerateSaleForTermSignatureResource extends Resource
{
    /** @inheritDoc */
    public function toArray($request): array
    {
        return [
            'message' => $this->resource['mensagem'] ?? null,
        ];
    }
}

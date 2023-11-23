<?php

namespace Recommendation\Adapters;

use Recommendation\Models\Recommendation;

class IndicatedAdapter
{
    public static function adapter(?Recommendation $recommendation): array
    {
        if ($recommendation !== null) {
            return $recommendation->toArray();
        }
        return [];
    }
}

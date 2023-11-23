<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

class PagtelUtilsAdapter
{
    /**
     * @param mixed[] $utils
     * @return mixed[]
     */
    public static function adapt(array $utils): array
    {
        $operatorsCode    = data_get($utils, 'fromOperators', []);
        $adaptedOperators = self::formatOperators($operatorsCode);

        return [
            'operators' => $adaptedOperators
        ];
    }

    /**
     * @param array[] $operators
     * @return array[]
     */
    private static function formatOperators(array $operators = []): array
    {
        $items = [];
        foreach ($operators as $code => $operator) {
            $items[] = [
                'id' => $code,
                'label' => $operator,
            ];
        }
        return $items;
    }
}

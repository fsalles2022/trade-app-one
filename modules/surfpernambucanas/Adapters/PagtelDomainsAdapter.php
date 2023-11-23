<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

class PagtelDomainsAdapter
{
    /**
     * @param mixed[] $domains
     * @return mixed[]
     */
    public static function adapt(array $domains): array
    {
        $localItems = data_get($domains, 'local', []);

        return [
            'local' => self::formatLocals($localItems),
        ];
    }

    /**
     * @param array[] $locals
     * @return array[]
     */
    private static function formatLocals(array $locals = []): array
    {
        $items = [];
        foreach ($locals as $code => $local) {
            $items[] = [
                'id' => $code,
                'label' => $local,
            ];
        }
        return $items;
    }
}

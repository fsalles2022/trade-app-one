<?php

namespace TradeAppOne\Domain\Services\Traits;

trait ArrayHelper
{
    /**
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com
     * Pequena modificação feita por Daniel Guiomarino
     */
    public function array_merge_recursive_distinct(array $array1, array $array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = $this->array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    public function array_intersect_recursive(array $array, array $filter): array
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                if (array_key_exists($key, $filter)) {
                    $array[$key] = $this->array_intersect_recursive($array[$key], $filter[$key]);
                } else {
                    unset($array[$key]);
                }
            } elseif (! in_array($val, $filter, true)) {
                unset($array[$key]);
            }
        }

        if (count(array_keys($array)) > 0 && is_numeric(array_keys($array)[0])) {
            return array_values($array);
        }

        return $array;
    }
}

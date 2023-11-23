<?php


namespace TradeAppOne\Domain\Repositories\Filters;

class BaseFilters
{
    public function apply(array $filters)
    {
        foreach ($filters as $key => $value) {
            if ($this->filterAvailable($key)) {
                $this->{$key}($value);
            }
        }

        return $this;
    }

    protected function filterAvailable($filter): bool
    {
        return in_array($filter, get_class_methods(get_called_class()));
    }
}

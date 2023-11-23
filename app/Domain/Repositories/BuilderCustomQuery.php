<?php

use Illuminate\Database\Eloquent\Builder;

Builder::macro('whereInLike', function (string $column, array $values) {
    $this->where(function (Builder $query) use ($column, $values) {
        foreach ($values as $value) {
            $query->orWhere($column, 'like', "%$value%");
        }
    });

    return $this;
});

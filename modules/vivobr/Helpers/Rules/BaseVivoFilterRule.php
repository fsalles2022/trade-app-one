<?php


namespace VivoBR\Helpers\Rules;

use Illuminate\Support\Collection;

interface BaseVivoFilterRule
{
    public function hasToFilter(string $cnpj, string $network): bool;

    public function filter(Collection $plans): Collection;
}

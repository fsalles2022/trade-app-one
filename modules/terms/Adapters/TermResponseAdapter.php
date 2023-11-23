<?php

declare(strict_types=1);

namespace Terms\Adapters;

use Terms\Models\Term;
use Terms\Models\UserTerm;

final class TermResponseAdapter
{
    /** @var mixed[] $adapted; */
    private $adapted;

    public function adapt(?Term $term, ?UserTerm $userTerm): self
    {
        $this->adapted = [
            'term' => is_null($term) ? [] : $term->toArray(),
            'userStatus' => $userTerm->status ?? null
        ];

        return $this;
    }

    /** @return mixed[] */
    public function toArray(): array
    {
        return $this->adapted;
    }
}

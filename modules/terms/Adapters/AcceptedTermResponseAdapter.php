<?php

declare(strict_types=1);

namespace Terms\Adapters;

use Terms\Enums\StatusUserTermsEnum;
use Terms\Models\UserTerm;

class AcceptedTermResponseAdapter
{
    /** @var mixed[] $adapted */
    private $adapted;

    public function adapt(?UserTerm $userTerm): self
    {
        $this->adapted = [
            'accepted' => ($userTerm->status ?? '') === StatusUserTermsEnum::CHECKED,
            'userId'   => $userTerm->userId ?? null,
            'termId'   => $userTerm->termId ?? null
        ];
        return $this;
    }

    public function toArray(): array
    {
        return $this->adapted;
    }
}

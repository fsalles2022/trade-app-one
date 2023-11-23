<?php

namespace Uol\Models;

class UolPassport
{
    public $id;
    public $number;
    protected $confirmed;
    protected $cancelled;

    public function __construct(string $id = null, string $passport = null, bool $confirmed = false)
    {
        $this->id        = $id;
        $this->number    = $passport;
        $this->confirmed = $confirmed;
        $this->cancelled = false;
    }

    public function isNotConfirmed(): bool
    {
        return $this->confirmed == false;
    }

    public function setStatus(bool $status): UolPassport
    {
        $this->confirmed = $status;
        return $this;
    }

    public function setCancel(bool $status = true) : UolPassport
    {
        $this->cancelled = $status;
        return $this;
    }
}

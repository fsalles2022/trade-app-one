<?php

namespace TradeAppOne\Policies;

use ErrorException;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\User;

class SalePolicy extends BasePolicy
{
    protected $module = 'Sale';
    protected $client;

    public function before()
    {
        parent::before();
        $this->client = strtolower(request()->header('client'));
    }

    public function view(User $user, Sale $sale)
    {
        try {
            $permissions = $user->role->permissions;
            return in_array('view', $permissions[$this->client][$this->module]);
        } catch (ErrorException $exception) {
            return false;
        }
    }

    public function create(User $user, Sale $sale)
    {
        try {
            return in_array('create', $user->role->permissions[$this->client][$this->module]);
        } catch (ErrorException $exception) {
            return false;
        }
    }
}

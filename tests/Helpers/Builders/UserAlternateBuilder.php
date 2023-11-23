<?php


namespace TradeAppOne\Tests\Helpers\Builders;


use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;

class UserAlternateBuilder
{
    private $user;
    private $document;

    public static function make(): UserAlternateBuilder
    {
        return new self();
    }

    public function withUser(User $user): UserAlternateBuilder
    {
        $this->user = $user;
        return $this;
    }

    public function withCustomDocument(string $document): UserAlternateBuilder
    {
        $this->document = $document;
        return $this;
    }

    public function build(): UserAuthAlternates
    {
        $attributes = [];

        if($this->user){
            $userId = data_get($this->user, 'id', null);
            data_set($attributes,'userId', $userId);
        }

        if($this->document){
            data_set($attributes,'document', $this->document);
        }

        return factory(UserAuthAlternates::class)->create($attributes);
    }
}
<?php

declare(strict_types=1);

namespace Bulletin\Tests\Helpers\Builders;

use Bulletin\Models\Bulletin;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;

class BulletinBuilder
{
    private $attributes = [];
    private $network;

    public static function make(): BulletinBuilder
    {
        return new self();
    }

    /**
     * @param Mixed[] $attributes
     * @return $this
     */
    public function withAttributes(array $attributes): BulletinBuilder
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param Network|null $network
     * @return $this
     */
    public function withNetwork(?Network $network): BulletinBuilder
    {
        $this->network = ($network instanceof Network)
            ? $network
            : (new NetworkBuilder())->build();

        $this->attributes['networkId'] = $this->network;

        return $this;
    }

    /**
     * @return Bulletin
     */
    public function build(): Bulletin
    {
        return factory(Bulletin::class)->create($this->attributes);
    }
}

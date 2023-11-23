<?php


namespace TradeAppOne\Tests\Helpers\Builders;


use TradeAppOne\Domain\Models\Tables\Channel;

class ChannelBuilder
{
    private $channel;

    public static function make(): ChannelBuilder
    {
        return new self();
    }

    public function withChannelName(string $channel): ChannelBuilder
    {
        $this->channel = $channel;
        return $this;
    }

    public function build(): Channel
    {
        $attributes = array_filter([
            'name' => $this->channel
        ]);
        return factory(Channel::class)->create($attributes);
    }
}
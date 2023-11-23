<?php

namespace TradeAppOne\Domain\Logging;

use Illuminate\Support\Facades\Log;

class IntegrationConcrete implements IntegrationLogger
{
    private $context = [];
    private $extra   = [];
    private $message;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    public function fire()
    {
        try {
            $user                  = request()->user();
            $this->context['user'] = $user ? $user->toArray() : [];
            $this->context         = array_filter($this->context);
            Log::info($this->message, $this->context, $this->extra);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function transaction($transaction): IntegrationLogger
    {
        $this->context['tags']['serviceTransaction'] = $transaction;
        return $this;
    }

    public function request($request): IntegrationLogger
    {
        $this->extra['request'] = json_encode($request, JSON_PRETTY_PRINT);
        return $this;
    }

    public function response($response): IntegrationLogger
    {
        $this->extra['response'] = json_encode($response, JSON_PRETTY_PRINT);
        return $this;
    }


    public function tags(array $tags): IntegrationLogger
    {
        try {
            if (isset($this->context['tags'])) {
                $this->context['tags'] = array_merge($this->context['tags'], $tags);
            } else {
                $this->context['tags'] = $tags;
            }
        } catch (\ErrorException $exception) {
            $this->context['tags'] = $tags;
        }
        return $this;
    }

    public function tag(string $key, string $value): IntegrationLogger
    {
        $this->context['tags'][$key] = $value;
        return $this;
    }

    public function extra(array $extra): IntegrationLogger
    {
        $this->extra = array_merge($this->extra, $extra);
        return $this;
    }

    public function message(string $message = ''): IntegrationLogger
    {
        $this->message = $message;
        return $this;
    }

    public function context(array $context): IntegrationLogger
    {
        if (is_array($this->context)) {
            $this->context = array_merge($this->context, $context);
        } else {
            $this->context = $context;
        }
        return $this;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

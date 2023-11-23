<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

use Gateway\API\Gateway;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class HeimdallConcrete implements Heimdall
{
    private $start;
    private $end;
    private $context = [];

    public function realm(string $realm): Heimdall
    {
        $this->context['realm'] = $realm;
        return $this;
    }

    public function request($request): Heimdall
    {
        if (is_array($request)) {
            $this->context['request'] = json_encode($request);
        } else {
            $this->context['request'] = $request;
        }
        return $this;
    }

    /**
     * @param null|ResponseInterface $response
     * @return Heimdall
     */
    public function response($response): Heimdall
    {
        if ($response && $response instanceof ResponseInterface) {
            $this->context['response'] = (string) $response->getBody();
            $this->context['status']   = $response->getStatusCode();
        } elseif ($response && is_array($response)) {
            $this->context['response'] = json_encode($response, JSON_PRETTY_PRINT);
        } elseif (method_exists($response, 'toArray')) {
            $this->context['response'] = json_encode($response->toArray(), JSON_PRETTY_PRINT);
        } elseif (method_exists($response, 'jsonSerialize')) {
            $this->context['response'] = json_encode($response->jsonSerialize(), JSON_PRETTY_PRINT);
        } elseif (method_exists($response, 'getResponse')) {
            $this->context['response'] = json_encode($response->getResponse(), JSON_PRETTY_PRINT);
        } elseif (is_string($response)) {
            $this->context['response'] = $response;
        } else {
            $this->context['response'] = '';
        }
        return $this;
    }

    public function url(?string $url): Heimdall
    {
        $this->context['url'] = $url;
        return $this;
    }

    /**
     * @param Client|null $client
     * @return Heimdall
     */
    public function httpClient($client): Heimdall
    {
        try {
            if ($client && $client instanceof Client) {
                $originalConfigs             = $client->getConfig();
                $configs['headers']          = (array) $originalConfigs['headers'];
                $configs['base_uri']         = isset($originalConfigs['base_uri']) ? (string) $originalConfigs['base_uri'] :
                    '';
                $this->context['httpClient'] = $configs;
            } elseif ($client && $client instanceof Gateway) {
                $configs['headers']          = [];
                $configs['base_uri']         = 'API-SDK';
                $this->context['httpClient'] = $configs;
            } else {
                $this->context['httpClient'] = '';
            }
        } catch (\Exception $exception) {
            $this->context['httpClient'] = $exception->getMessage();
        }
        return $this;
    }

    public function method(?string $method = ''): Heimdall
    {
        $this->context['method'] = $method;
        return $this;
    }

    public function fire()
    {
        $this->context['session']   = $this->session();
        $this->context['user']      = $this->user();
        $this->context['requestId'] = request()->header('requestId');
        $message                    = $this->message ?? 'Request';
        try {
            if (is_float($this->start) && is_float($this->end)) {
                $this->context['executionTime'] = number_format($this->end - $this->start, 2);
            }
            $request  = data_get($this->context, 'request');
            $response = data_get($this->context, 'response');

            $removeBackslash           = "/\\\\/";
            $removeBreakLines          = "/\r|\n/";
            $this->context['request']  = preg_replace($removeBackslash, "", $request);
            $this->context['response'] = preg_replace($removeBreakLines, "", $response);

            logger()->channel('heimdall')->info($message, $this->context);
        } catch (\Exception $exception) {
            if (! $exception instanceof \RuntimeException) {
                logger()->alert(
                    'ElasticSearch is down: ' . $exception->getMessage(),
                    ['message' => $message, 'context' => $this->context]
                );
            }
        }
    }

    private function session(): array
    {
        $session['ip']         = request()->getClientIp();
        $session['headers']    = (array) request()->headers;
        $session['method']     = request()->getMethod();
        $session['requestUri'] = request()->getRequestUri();
        $session['requestId']  = request()->header('requestId');
        $session['path']       = request()->path();
        $requestParams         = request()->all();
        $requestSanitized      = $this->sanitizeCreditCardData($requestParams);
        $session['params']     = json_encode($requestSanitized);
        return $session;
    }

    private function user(): array
    {
        $user       = request()->user();
        $mappedUser = HeimdallUserMapper::map($user);
        return $mappedUser;
    }

    /**
     * @param \Exception|null $exception
     * @return Heimdall
     */
    public function catchException($exception): Heimdall
    {
        $this->message = $exception->getMessage();
        return $this;
    }

    public function start($start): Heimdall
    {
        $this->start = $start;
        return $this;
    }

    public function end($end): Heimdall
    {
        $this->end = $end;
        return $this;
    }

    public function executionTime($executionTime): Heimdall
    {
        $this->context['executionTime'] = $executionTime;
        return $this;
    }

    private function sanitizeCreditCardData(array $requestParams): array
    {
         $creditCardPan = data_get($requestParams, 'creditCard.pan', null);
        if ($creditCardPan !== null) {
            $sanitized = substr($creditCardPan, 0, 12) . '****';
            data_set($requestParams, 'creditCard.pan', $sanitized);
        }
         return $requestParams;
    }
}

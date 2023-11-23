<?php

declare(strict_types=1);

namespace Tradehub;

class TradeHubHeaders
{
    /** @var string */
    private $uri;

    /** @var string[] */
    private $headers;

    /** @var string */
    private $key;

    /** @var string */
    private $secret;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $captchaKey;

    /** @var string */
    private $viaVarejoSellerPassword;

    public function __construct(array $configs)
    {
        $this->uri                      = data_get($configs, 'uri');
        $this->key                      = data_get($configs, 'key', '');
        $this->secret                   = data_get($configs, 'secret', '');
        $this->login                    = data_get($configs, 'login', '');
        $this->password                 = data_get($configs, 'password', '');
        $this->captchaKey               = data_get($configs, 'captcha-key', '');
        $this->viaVarejoSellerPassword  = data_get($configs, 'via-varejo-seller-password', '');
        $this->headers                  = data_get($configs, 'headers', []);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /** @return string[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getCaptchaKey(): string
    {
        return $this->captchaKey;
    }

    public function getViaVarejoSellerPassword(): string
    {
        return $this->viaVarejoSellerPassword;
    }

    /** @return string[] */
    public function getCredentials(): array
    {
        return [
            'apiKey' => $this->key,
            'secret' => $this->secret
        ];
    }

    /** @return string[] */
    public function getCredentialsSeller(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password
        ];
    }
}

<?php


namespace Outsourced\Partner\Connections;

class ViaVarejoHeaders
{
    private $uri;
    private $scope;
    private $grantType;
    private $canalVenda;
    private $username;
    private $password;
    private $key;
    private $headers;

    public function __construct($configs)
    {
        $this->uri        = $configs['uri'];
        $this->scope      = $configs['scope'];
        $this->grantType  = $configs['grant_type'];
        $this->canalVenda = $configs['canal_venda'];
        $this->username   = $configs['username'];
        $this->password   = $configs['password'];
        $this->key        = $configs['key'];
        $this->headers    = $configs['headers'];
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getGrantType()
    {
        return $this->grantType;
    }

    public function getCanalVenda()
    {
        return $this->canalVenda;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}

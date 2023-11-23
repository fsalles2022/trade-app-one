<?php

namespace VivoBR\Repositories;

use TradeAppOne\Domain\Services\Interfaces\UserThirdPartyRepository;
use VivoBR\Adapters\Response\UserResponseAdapter;
use VivoBR\Connection\SunConnection;
use VivoBR\Exceptions\VivoBRAPIPersistenceException;

class VivoBRUserRepository implements UserThirdPartyRepository
{
    const SUCCESS_CODE = '0';
    const USER_KEY     = 'usuario';
    protected $connection;

    public function __construct(SunConnection $connection)
    {
        $this->connection = $connection;
    }

    public function createOrUpdate(array $attributes, $network = null): array
    {
        $query            = $attributes;
        $query['network'] = $network;
        $user             = $this->findUser($query);
        if (filled($user) && data_get($user, 'cpf') == data_get($attributes, 'cpf')) {
            $result = $this->connection->selectCustomConnection($network)->updateUser($attributes);
            $action = self::UPDATED;
        } else {
            $result = $this->connection->selectCustomConnection($network)->createUser($attributes);
            $action = self::CREATED;
        }
        $adapter = new UserResponseAdapter($result);
        if (! $adapter->isSuccess()) {
            throw new VivoBRAPIPersistenceException(data_get($adapter->getAdapted(), 'message'));
        }
        return [$action, $adapter->isSuccess()];
    }

    public function findUser(array $keywords): array
    {
        $network  = data_get($keywords, 'network');
        $cpf      = data_get($keywords, 'cpf');
        $response = $this->connection->selectCustomConnection($network)->getUser($cpf)->toArray();
        $found    = data_get($response, 'codigo') == self::SUCCESS_CODE;
        if ($found) {
            return data_get($response, self::USER_KEY);
        }
        return [];
    }
}

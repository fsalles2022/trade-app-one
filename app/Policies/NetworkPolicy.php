<?php


namespace TradeAppOne\Policies;

use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\NetworkPermission;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Exceptions\SystemExceptions\NetworkExceptions;
use TradeAppOne\Facades\UserPolicies;

class NetworkPolicy
{
    public const PARENT_DEFAULT_HIERARCHY = 1;
    public const PARENT_DEFAULT_ROLE      = 2;

    protected $hierarchyRepository;
    protected $roleRepository;
    protected $availableServiceRepository;

    public function __construct(HierarchyRepository $hierarchyRepository, RoleRepository $roleRepository)
    {
        $this->hierarchyRepository = $hierarchyRepository;
        $this->roleRepository      = $roleRepository;
    }

    public function create(array $payload): void
    {
        $attributes = $this->adapterNetwork($payload);
        $network    = NetworkRepository::create($attributes);

        $this->hierarchyRepository->create($this->adapterHierarchy($network));
        $this->roleRepository->create($this->adapterRole($network));

        $availableServices = data_get($payload, 'availableServices');
        $servicesIds       = $this->getChosenServicesIds($availableServices);

        $network->services()->sync($servicesIds);
    }

    private function adapterNetwork(array $attributes): array
    {
        $this->validate($attributes);

        return [
            'slug'               => data_get($attributes, 'slug'),
            'label'              => data_get($attributes, 'label'),
            'cnpj'               => BrazilianDocuments::validateCnpj($attributes['cnpj']),
            'tradingName'        => data_get($attributes, 'tradingName'),
            'companyName'        => data_get($attributes, 'companyName'),
            'zipCode'            => data_get($attributes, 'zipCode'),
            'local'              => data_get($attributes, 'local'),
            'neighborhood'       => data_get($attributes, 'neighborhood'),
            'state'              => data_get($attributes, 'state'),
            'city'               => data_get($attributes, 'city'),
            'number'             => data_get($attributes, 'number'),
            'channel'            => data_get($attributes, 'channel'),
        ];
    }

    private function validate(array $attributes): void
    {
        $availableServices = Operations::SECTORS;
        $servicesPayload   = data_get($attributes, 'availableServices');

        if (! $this->validatedServices($availableServices, $servicesPayload)) {
            throw NetworkExceptions::availableServiceNotFound();
        }

        $permission = NetworkPermission::getFullName(NetworkPermission::CREATE);
        UserPolicies::hasPermission($permission);
    }

    public function validatedServices(array $availableServices, array $servicesPayload): bool
    {
        $results = [];

        foreach ($availableServices as $availableService) {
            foreach ($servicesPayload as $services) {
                foreach ($services as $key => $service) {
                    foreach ($service as $operator) {
                        if (array_key_exists($key, $availableService)) {
                            $results[] = array_key_exists($operator, $availableService[$key]);
                        }
                    }
                }
            }
        }

        return in_array(false, $results) == false ? true : false;
    }

    private function adapterHierarchy(Network $network): array
    {
        return [
            'parent'    => self::PARENT_DEFAULT_HIERARCHY,
            'label'     => $network->label,
            'slug'      => 'rede-' . $network->slug,
            'networkId' => $network->id
        ];
    }

    private function adapterRole(Network $network): array
    {
        return [
            'name'      => 'Diretor',
            'slug'      => 'diretor-' . $network->slug,
            'networkId' => $network->id,
            'parent'    => self::PARENT_DEFAULT_ROLE
        ];
    }

    private function getChosenServicesIds(array $availableServices): array
    {
        $pluck = [];

        foreach ($availableServices as $availableService) {
            foreach ($availableService as $operator => $values) {
                foreach ($values as $key => $operation) {
                    $pluck[] = Service::query()->where([
                        'operator'   => $operator,
                        'operation' => $operation
                    ])->first()->id;
                }
            }
        }

        return $pluck;
    }

    private function setNetworkChannel($attributes, Network $network): void
    {
        $channels   = collect(data_get($attributes, 'channel'));
        $channelsId = [];
        $channels->each(static function ($value) use (&$channelsId) {
            $channelsId[] = Channel::query()->where(['name' => $value])->first()->id;
        });
        $network->channels()->sync($channelsId);
    }
}

<?php

namespace TradeAppOne\Domain\Importables;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Components\Helpers\StringHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;

class UserImportable implements ImportableInterface
{
    protected $userService;
    public $businessRules;

    protected $permission;
    protected $authenticatedUserNetwork;

    private const NETWORKS_MATRICULATION = [
        NetworkEnum::VIA_VAREJO,
        NetworkEnum::GPA,
        NetworkEnum::EXTRA,
        NetworkEnum::RIACHUELO,
        NetworkEnum::TRADE_APP_ONE,
    ];

    public function __construct(UserService $userService, BusinessRules $businessRules)
    {
        $this->userService              = $userService;
        $this->businessRules            = $businessRules;
        $this->authenticatedUserNetwork = $this->userService
            ->getAuthenticatedUser()
            ->getNetwork();
    }

    public function getExample(User $user = null, string $cnpj = null, Role $role = null, string $hierarchy = null, string $matriculation = null): array
    {
        $userRole = data_get($role, 'slug') ?? data_get($user, 'role.slug');

        $example = [
            $user->firstName ?? 'Joao',
            $user->lastName ?? 'Silva',
            $user->email ?? 'vendedor@email.com',
            $user->cpf ?? '10816182051',
            $user->birthday ?? '04/01/1996',
            $userRole ?? 'vendedor',
            $cnpj ?? '22696923000162',
            $hierarchy ?? 'regional-1'
        ];

        if ($this->networksShouldBeUseMatriculation()) {
            $example['matriculation'] = $matriculation ?? '123456';
        }

        return $example;
    }

    public function getColumns(): array
    {
        $columns = [
            'firstName' => trans('importables.user.firstName'),
            'lastName'  => trans('importables.user.lastName'),
            'email'     => 'email',
            'cpf'       => 'cpf',
            'birthday'  => trans('importables.user.birthday'),
            'role'      => trans('importables.user.role'),
            'cnpj'      => trans('importables.user.pointOfSale'),
            'hierarchy' => trans('importables.user.hierarchy')
        ];

        if ($this->networksShouldBeUseMatriculation()) {
            $columns['matriculation'] = trans('importables.user.matriculation');
        }

        return $columns;
    }

    public function networksShouldBeUseMatriculation(): bool
    {
        $network = Auth::user()->pointsOfSale()->first()->network->slug;

        return in_array($network, self::NETWORKS_MATRICULATION, true);
    }

    public function processLine($line): User
    {
        $cpf  = BrazilianDocuments::validateCpf($line['cpf']);
        $user = $this->userService->findOneByCpfWithTrashed($cpf);

        return $user === null
            ? $this->createUser($line)
            : $this->updateUser($user, $line);
    }

    private function createUser(array $line): User
    {
        $userData = $this->adapterLine($line);

        $this->defaultValidations($userData);

        return $this->userService->createUser($userData);
    }

    private function updateUser(User $user, array $line): User
    {
        $userData = $this->adapterLine($line, true);

        $this->defaultValidations($userData)
            ->hasAuthorizationUnderUser($user);

        return $this->userService->updateUser($user, $userData);
    }

    private function adapterLine(array $line, bool $update = false): array
    {
        $birthday      = data_get($line, 'birthday');
        $cpf           = data_get($line, 'cpf');
        $role          = data_get($line, 'role');
        $email         = data_get($line, 'email');
        $lastName      = data_get($line, 'lastName');
        $firstName     = data_get($line, 'firstName');
        $cnpj          = data_get($line, 'cnpj');
        $hierarchy     = data_get($line, 'hierarchy');
        $matriculation = data_get($line, 'matriculation');

        return array_filter([
            'firstName'     => strtoupper(StringHelper::removeSpecialcharactersAndAccent($firstName)),
            'lastName'      => strtoupper(StringHelper::removeSpecialcharactersAndAccent($lastName)),
            'email'         => $email,
            'cpf'           => BrazilianDocuments::validateCpf($cpf),
            'birthday'      => DateConvertHelper::validateAndConvertOfDMY($birthday),
            'pointOfSale'   => $cnpj,
            'role'          => $role,
            'hierarchy'     => $hierarchy,
            'matriculation' => $matriculation
        ]);
    }

    private function defaultValidations(array $userData)
    {
        $pointOfSale = data_get($userData, 'pointOfSale');
        $hierarchy   = data_get($userData, 'hierarchy');
        $role        = data_get($userData, 'role');

        if (empty($pointOfSale) and empty($hierarchy)) {
            throw new \InvalidArgumentException(trans('exceptions.userImportable.noHierarchyAndPdv'));
        }

        $networkRules = $this->businessRules->network()
            ->setNetworkByRoleSlug($role);

        $userRules = $this->businessRules->user()
            ->hasAuthorizationUnderRole($role);

        if ($hierarchy) {
            $userRules->hasAuthorizationUnderHierarchy($hierarchy);
            $networkRules->belongsToHierarchy($hierarchy);
        }

        if ($pointOfSale) {
            $userRules->hasAuthorizationUnderPointOfSale($pointOfSale);
            $networkRules->belongsToPointOfSale($pointOfSale);
        }

        return $userRules;
    }

    public function getType(): string
    {
        return Importables::USERS;
    }
}

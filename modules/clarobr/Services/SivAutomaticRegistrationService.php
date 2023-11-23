<?php

declare(strict_types=1);

namespace ClaroBR\Services;

use ClaroBR\Adapters\AutomaticRegistrationResponseAdapter;
use ClaroBR\Connection\SivConnection;
use ClaroBR\Exceptions\SivAutomaticRegistrationExceptions;
use ClaroBR\Exceptions\SivAutomaticRegistrationGenericException;
use ClaroBR\Exceptions\SivInvalidCredentialsException;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\NetworkRepository;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\RoleRepository;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\RoleNotFoundException;
use TradeAppOne\Mail\UserPasswordChangedMail;

class SivAutomaticRegistrationService
{
    /** @var SivConnection */
    protected $sivConnection;

    /** @var PointOfSaleRepository */
    protected $pointOfSaleRepository;

    /** @var RoleRepository */
    protected $roleRepository;

    /** @var UserService */
    protected $userService;

    /** @var NetworkRepository */
    private $networkRepository;

    public function __construct(
        SivConnection $sivConnection,
        PointOfSaleRepository $pointOfSaleRepository,
        RoleRepository $roleRepository,
        UserService $userService,
        NetworkRepository $networkRepository
    ) {
        $this->sivConnection         = $sivConnection;
        $this->pointOfSaleRepository = $pointOfSaleRepository;
        $this->userService           = $userService;
        $this->roleRepository        = $roleRepository;
        $this->networkRepository     = $networkRepository;
    }

    /**
     * @throws Throwable
     * @param mixed[] $attributes
     */
    public function automaticRegistration(array $attributes): void
    {
        try {
            DB::beginTransaction();

            $this->processAutomaticRegistration($attributes);

            DB::commit();
        } catch (SivAutomaticRegistrationGenericException $e) {
            throw $e;
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    /** @param mixed[] $attributes */
    private function processAutomaticRegistration(array $attributes): void
    {
        $this->preCheckUser(data_get($attributes, 'usuario', []));

        $pointOfSale = $this->getPointOfSaleByCnpj($attributes);
        $network     = $pointOfSale->network;

        $attributes['pdv']['idpdv'] = $pointOfSale->id;

        $hierarchyFromNetwork = $this->getParentHierarchy(data_get($network, 'hierarchies'));
        $role                 = $this->getRoleFromNetwork($attributes, $network);

        $userData = $this->adaptUser($attributes);

        $responseCreateUser = $this->userService->createUserWithAutomaticRegistration($pointOfSale, $hierarchyFromNetwork, $role, $userData);

        $user = data_get($responseCreateUser, 'user');

        $hashedPassword = data_get($responseCreateUser, 'hashedPassword');

        throw_if(! $user, SivAutomaticRegistrationExceptions::userNotBeCreated());

        $hasCodeClaro = $this->checkCodeClaroExists($pointOfSale);

        Mail::send(new UserPasswordChangedMail($user, $hashedPassword, false));

        if ($hasCodeClaro && ($pointOfSale->servicesClaro()->isNotEmpty() || $network->servicesClaro()->isNotEmpty())) {
            $attributes['pdv']['codigo'] = $pointOfSale->providerIdentifiers[Operations::CLARO];
            $response                    = $this->sivConnection->sendAutomaticRegistration($user, $attributes);

            throw_if(
                $response->getStatus() !== Response::HTTP_CREATED,
                SivAutomaticRegistrationExceptions::genericSivError($response)
            );
        }
    }

    private function checkCodeClaroExists(PointOfSale $p): bool
    {
        $hasCodeClaro = $p->providerIdentifiers[Operations::CLARO] ?? false;

        return (bool) $hasCodeClaro;
    }

    private function validateIfPointOfSaleExistsInNetwork(PointOfSale $pointOfSale, Network $network): void
    {
        throw_unless(
            $pointOfSale->network()->first()->cnpj === $network->cnpj,
            SivAutomaticRegistrationExceptions::pointOfSaleNotExistsInNetwork()
        );
    }

    /** @param mixed[] $attributes */
    private function getPointOfSaleByCnpj(array $attributes): PointOfSale
    {
        $pointOfSale = $this->pointOfSaleRepository->findOneByCnpj($attributes['pdv']['cnpj'] ?? '');

        throw_unless($pointOfSale instanceof PointOfSale, PointOfSaleNotFoundException::class);

        return $pointOfSale;
    }

    /** @param mixed[] $attributes */
    public function getNetworkByCnpj(array $attributes): Network
    {
        $network = $this->networkRepository->findOneBy('cnpj', $attributes['rede']['cnpj'] ?? '')->first();

        throw_unless($network instanceof Network, NetworkNotFoundException::class);

        return $network;
    }

    /** @param mixed[] $pointOfSaleAttributes */
    private function getPointOfSaleByCode(array $pointOfSaleAttributes): PointOfSale
    {
        $pointOfSaleCode = data_get($pointOfSaleAttributes, 'codigo');

        $pointOfSale = $this->getPointOfSaleByProviderIdentifiers($pointOfSaleCode);

        if ($pointOfSale === null) {
            $pointOfSaleCodeExploded   = explode('-', $pointOfSaleCode);
            $pointOfSaleCodeWithoutDdd = current($pointOfSaleCodeExploded);

            if ($pointOfSaleCodeWithoutDdd !== $pointOfSaleCode) {
                $pointOfSale = $this->getPointOfSaleByProviderIdentifiers($pointOfSaleCodeWithoutDdd);
            }
        }

        if ($pointOfSale === null) {
            throw new PointOfSaleNotFoundException();
        }

        return $pointOfSale;
    }

    private function getPointOfSaleByProviderIdentifiers(string $value): ?PointOfSale
    {
        return $this->pointOfSaleRepository->findOneByProviderIdentifiers(
            Operations::CLARO,
            $value
        );
    }

    private function getParentHierarchy(Collection $hierarchies): ?Hierarchy
    {
        if ($hierarchies->isNotEmpty()) {
            return $hierarchies->where('sequence', $hierarchies->min('sequence'))->first();
        }
        return null;
    }

    /**
     * @param mixed[] $userAttributes
     * @throws Throwable
     */
    private function preCheckUser(array $userAttributes): void
    {
        $cpf  = BrazilianDocuments::validateCpf(data_get($userAttributes, 'cpf'));
        $user = $this->userService->findOneByCpfWithTrashed($cpf);
        throw_if($user !== null, SivAutomaticRegistrationExceptions::userAlreadyExists());
    }

    /** @param mixed[] $attributes */
    private function getRoleFromNetwork(array $attributes, Network $network): Role
    {
        $role = $this->getRoleByNameAndNetwork($attributes, ((string) $network->id) ?? null);

        throw_unless($role instanceof Role, RoleNotFoundException::class);

        return $role;
    }

    /** @param mixed[] $attributes */
    private function getRoleByNameAndNetwork(array $attributes, ?string $networkId): ?Role
    {
        return $this->roleRepository->findOneWithFilters([
            'name'      => $attributes['usuario']['perfil'] ?? null,
            'networkId' => $networkId
        ]);
    }

    private function getStructuralRole(Collection $roles): ?Role
    {
        throw_if(
            ! $roles instanceof Collection || $roles->isEmpty(),
            SivAutomaticRegistrationExceptions::notHaveRolesFromUser()
        );

        return $roles->filter(function (Role $role) {
            if (str_contains($role->slug, 'vendedor') && ! str_contains($role->slug, 'promotor')) {
                return $role;
            }
        })->first();
    }

    private function getOutRole(Collection $roles): ?Role
    {
        throw_if(
            ! $roles instanceof Collection || $roles->isEmpty(),
            SivAutomaticRegistrationExceptions::notHaveRolesFromUser()
        );

        return $roles->filter(static function (Role $role) {
            if (str_contains($role->slug, 'vendedor-promotor-inova')) {
                return $role;
            }
        })->first();
    }

    /**
     * @param mixed[] $attributes
     * @return mixed[]
     */
    private function adaptUser(array $attributes): array
    {
        $targetedName = $this->getUserNames(data_get($attributes, 'usuario.nome', ''));

        return [
            'firstName'     => strtoupper(data_get($targetedName, 'firstName')),
            'lastName'      => strtoupper(data_get($targetedName, 'lastName')),
            'email'         => data_get($attributes, 'usuario.email'),
            'cpf'           => BrazilianDocuments::validateCpf(data_get($attributes, 'usuario.cpf')),
            'birthday'      => DateConvertHelper::validateAndConvertOfDMY(
                data_get($attributes, 'usuario.data_nascimento')
            ),
            'matriculation' => data_get($attributes, 'matriculation')
        ];
    }

    private function getUserNames(string $fullName): array
    {
        $names = [
            'firstName' => '',
            'lastName' => '',
        ];

        $exploded = explode(' ', $fullName);
        if (count($exploded) > 0) {
            $names['firstName'] = data_get($exploded, '0', '');
            unset($exploded[0]);
            $names['lastName'] = implode(' ', $exploded);
        }

        return $names;
    }

    /**
     * @throws SivInvalidCredentialsException
     */
    public function checkAutomaticRegistrationStatus(string $protocol): array
    {
        $user = $this->userService->findOneByAlternate($protocol);

        if (! $user instanceof User) {
            $user = $this->userService->findBy($protocol);
        }

        $sivUser = $this->sivConnection->checkAutomaticRegistrationStatus(data_get($user, 'cpf', ''))->toArray();

        if ($user === null || empty($sivUser)) {
            return AutomaticRegistrationResponseAdapter::adaptNotFound($protocol);
        }

        return AutomaticRegistrationResponseAdapter::adaptCreationSuccessful($user, $sivUser, $protocol);
    }
}

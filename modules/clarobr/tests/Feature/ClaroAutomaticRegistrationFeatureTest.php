<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Feature;

use ClaroBR\Exceptions\SivAutomaticRegistrationExceptions;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\AuthHelper;

class ClaroAutomaticRegistrationFeatureTest extends TestCase
{
    use BindInstance, SivFactoriesHelper, SivBindingHelper, AuthHelper;

    private const URL_AUTOMATIC_REGISTRATION = '/clarobr/automatic-registration';

    /** @test */
    public function should_search_point_of_sale_by_code_without_ddd_with_success(): void
    {
        $this->bindSivResponse();

        $user = $this->buildActiveUser();

        $payload = $this->getPayload();

        $this->buildStructToTestByAttributes([
            'hierarchy' => [
                'slug' => 'loja-regional'
            ],
            'pointOfSale' => [
                'providerIdentifiers' => json_encode(['CLARO' => data_get($payload, 'pdv.codigo')]),
            ],
            'role' => [
                'state' => 'salesman',
                'slug' => 'loja-vendedor',
            ],
        ]);

        Bus::fake();

        $this->authAs($user)
            ->post(self::URL_AUTOMATIC_REGISTRATION, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'protocol' => data_get($payload, 'usuario.cpf'),
                'message' => trans('siv::messages.automaticRegistration.received'),
            ]);
    }

    /** @test */
    public function should_search_point_of_sale_by_code_with_ddd_with_success(): void
    {
        $this->bindSivResponse();

        $user = $this->buildActiveUser();

        $this->buildStructToTestByAttributes([
            'hierarchy' => [
                'slug' => 'loja-regional'
            ],
            'pointOfSale' => [
                'providerIdentifiers' => json_encode(['CLARO' => '939V']),
            ],
            'role' => [
                'state' => 'salesman',
                'slug' => 'loja-vendedor',
            ],
        ]);
        
        $payload = $this->getPayload();

        data_set($payload, 'pdv.codigo', '939V-11');

        Bus::fake();

        $this->authAs($user)
            ->post(self::URL_AUTOMATIC_REGISTRATION, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'protocol' => data_get($payload, 'usuario.cpf'),
                'message' => trans('siv::messages.automaticRegistration.received'),
            ]);
    }

    /** @test */
    public function should_throw_error_search_point_of_sale_not_found(): void
    {
        $this->bindSivResponse();

        $user = $this->buildActiveUser();

        $this->buildStructToTestByAttributes([
            'hierarchy' => [
                'slug' => 'loja-regional'
            ],
            'pointOfSale' => [
                'providerIdentifiers' => json_encode(['CLARO' => 'OB3W']),
            ],
            'role' => [
                'state' => 'salesman',
                'slug' => 'loja-vendedor',
            ],
        ]);

        $payload = $this->getPayload();
        

        $this->authAs($user)
            ->post(self::URL_AUTOMATIC_REGISTRATION, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function should_throw_error_user_already_exists(): void
    {
        $user = $this->buildActiveUser();

        $payload = $this->getPayload();

        data_set($payload, 'usuario.cpf', $user->cpf);
        
        $this->authAs($user)
            ->post(self::URL_AUTOMATIC_REGISTRATION, $payload)
            ->assertStatus(Response::HTTP_PRECONDITION_FAILED)
            ->assertJsonFragment([
                'shortMessage' => SivAutomaticRegistrationExceptions::USER_ALREADY_EXISTS,
                'message'      => trans('siv::exceptions.AutomaticRegistration.' . SivAutomaticRegistrationExceptions::USER_ALREADY_EXISTS),
            ]);
    }

    public function test_should_throw_protocol_user_auth_alternate(): void
    {
        $user = $this->buildActiveUser();

        $payload = $this->getPayloadWithoutCentralizer();

        Bus::fake();

        $this->authAs($user)
            ->post(self::URL_AUTOMATIC_REGISTRATION, $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'protocol' => data_get($payload, 'usuario.autenticacaoAlternativa'),
                'message' => trans('siv::messages.automaticRegistration.received'),
            ]);
    }

    /** @return mixed[] */
    private function getPayloadWithoutCentralizer(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/Request/ClaroAutomaticRegistration/PayloadWithoutCentralizer.json'), true);
    }

    /** @return mixed[] */
    private function getPayload(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/Request/ClaroAutomaticRegistration/Success.json'), true);
    }

    /** @param mixed[] $attributes */
    private function buildStructToTestByAttributes(array $attributes): void
    {
        $hierarchy = HierarchyBuilder::make()
            ->withSlug(data_get($attributes, 'hierarchy.slug'))
            ->build();
        
        $network = NetworkBuilder::make()->build();
        
        $network->hierarchies()->save($hierarchy);

        PointOfSaleBuilder::make()
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->withParams([
                'providerIdentifiers' => data_get($attributes, 'pointOfSale.providerIdentifiers'),
            ])
            ->build();


        $role = RoleBuilder::make()
            ->withRoleState(data_get($attributes, 'role.state', ''))
            ->withNetwork($network)
            ->build();

        $role->slug = data_get($attributes, 'role.slug', 'loja');
        $role->save();
    }

    private function buildActiveUser(): User
    {
        return UserBuilder::make()
            ->withUserState('user_active')
            ->build();
    }
}

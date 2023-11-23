<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Feature\Job;

use ClaroBR\Exceptions\SivAutomaticRegistrationExceptions;
use ClaroBR\Jobs\ProcessAutomaticRegistration;
use ClaroBR\Services\SivAutomaticRegistrationService;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\UserRepository;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\TestCase;

class ProcessAutomaticRegistrationTest extends TestCase
{
    public function test_should_throw_exception_when_user_already_exists(): void
    {
        factory(User::class)->create([
            'firstName' => 'Brenda 27 Haruko',
            'lastName' => 'Brenda 27 Haruko Rodrigues Caldeira',
            'cpf' => '68453622540',
            'email' => 'cadastro@tradeupgroup.com'
        ]);

        $this->expectException(BuildExceptions::class);
        $this->expectExceptionMessage(SivAutomaticRegistrationExceptions::userAlreadyExists()->getMessage());
        $this->expectExceptionCode(SivAutomaticRegistrationExceptions::userAlreadyExists()->getCode());

        (new ProcessAutomaticRegistration($this->getPayload()))
            ->handle(resolve(SivAutomaticRegistrationService::class));
    }

    public function test_should_throw_exception_when_point_of_sale_not_found(): void
    {
        $this->expectException(PointOfSaleNotFoundException::class);
        $this->expectExceptionMessage((new PointOfSaleNotFoundException())->getMessage());
        $this->expectExceptionCode((new PointOfSaleNotFoundException())->getCode());

        (new ProcessAutomaticRegistration($this->getPayload()))
            ->handle(resolve(SivAutomaticRegistrationService::class));
    }
    
    public function test_should_save_user_successfully(): void
    {
        $this->createContext();

        (new ProcessAutomaticRegistration($this->getPayload()))
            ->handle(resolve(SivAutomaticRegistrationService::class));

        $user = resolve(UserRepository::class)->findOneBy('cpf', '68453622540');
        $this->assertNotEmpty($user);
        $this->assertInstanceOf(User::class, $user);
    }

    /** @return mixed[] */
    private function getPayload(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/../Request/ClaroAutomaticRegistration/Success.json'), true);
    }

    private function createContext(): void
    {
        $network = factory(Network::class)->create([
            'cnpj' => $this->getPayload()['rede']['cnpj'] ?? ''
        ]);

        $pointOfSale = factory(PointOfSale::class)->create([
            'tradingName' => 'LOG EXPRESS SP1',
            'label' => '939V',
            'providerIdentifiers' => '{"CLARO": "939V"}',
            'cnpj' => '13263506000305',
            'networkId' => $network->id
        ]);

        $role       = RoleBuilder::make()->withNetwork($network)->build();
        $role->name = $this->getPayload()['usuario']['perfil'] ?? '';
        $role->save();

        HierarchyBuilder::make()->withNetwork($network)->withPointOfSale($pointOfSale)->build();
    }
}

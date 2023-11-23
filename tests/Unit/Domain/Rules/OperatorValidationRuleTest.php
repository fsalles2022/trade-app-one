<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\OperatorValidationRule;
use TradeAppOne\Tests\Helpers\UserHelper;
use TradeAppOne\Tests\TestCase;

class OperatorValidationRuleTest extends TestCase
{
    use UserHelper;

    /** @test */
    public function should_return_true_when_user_has_availables_services_in_network()
    {
        $validServices = [
            'services' => [
                [
                  'operator' => Operations::CLARO,
                  'operation' => Operations::CLARO_CONTROLE_BOLETO
                ],
                [
                  'operator' => Operations::OI,
                  'operation' => Operations::OI_CONTROLE_BOLETO
                ]
            ]
        ];

        $values = $this->getMockBuilder('stdClass')->setMethods(['getData'])->getMock();
        $values->method('getData')->will($this->returnValue($validServices));

        $services[] = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_CONTROLE_BOLETO]);

        $services[] = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::OI,
            'operation' => Operations::OI_CONTROLE_BOLETO]);

        $user               = factory(User::class)->states('user_active')->make();
        $networkEntity      = factory(Network::class)->states('with_available_services')->create();
        $networkEntity->services()->sync(array_pluck($services, 'id', []));
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleOwnPointOfSale = factory(Role::class)->states('scope_own_network')->make();

        $userWithPermission = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleOwnPointOfSale, $user);

        Auth::shouldReceive('user')->andReturn($userWithPermission);
            
        $operatorValidation = new OperatorValidationRule();
        $userHasPermission  = $operatorValidation->passes(null, null, null, $values);

        $this->assertTrue($userHasPermission);
    }

    /** @test */
    public function should_return_false_when_user_hasnt_availables_services_in_network()
    {
        $invalidServices = [
            'services' => [
                [
                  'operator' => Operations::CLARO_CONTROLE_BOLETO,
                  'operation' => Operations::CLARO_CONTROLE_BOLETO
                ]
            ],
            'services' => [
                [
                  'operator' => Operations::CLARO_CONTROLE_BOLETO,
                  'operation' => 'OPERATION_NON_EXISTENT',
                ]
            ],
        ];

        $values = $this->getMockBuilder('stdClass')->setMethods(['getData'])->getMock();
        $values->method('getData')->will($this->returnValue($invalidServices));

        $user               = factory(User::class)->states('user_active')->make();
        $networkEntity      = factory(Network::class)->states('without_available_services')->create();
        $pointOfSaleFactory = factory(PointOfSale::class)->make();
        $roleOwnPointOfSale = factory(Role::class)->states('scope_own_network')->make();

        $userWithPermission = $this->associateUserRelations($networkEntity, $pointOfSaleFactory, $roleOwnPointOfSale, $user);

        Auth::shouldReceive('user')->andReturn($userWithPermission);
            
        $operatorValidation = new OperatorValidationRule();
        $userHasPermission  = $operatorValidation->passes(null, null, null, $values);

        $this->assertFalse($userHasPermission);
    }
}

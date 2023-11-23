<?php

namespace VivoBR\Tests\Unit\Assistances;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Assistances\VivoBRControleAssistance;
use VivoBR\Models\VivoControle;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;
use VivoBR\Tests\ServerTest\SunTestBook;

class VivoBRControleAssistanceTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_activate_service_with_success_vivobr()
    {
        $service = $this->buildSale();

        $received = $this->assistance()->integrateService($service);
        $message  = data_get($received->getOriginalContent(), 'message');

        $this->assertEquals(Response::HTTP_OK, $received->status());
        $this->assertEquals(trans('sun::messages.activation.'. Operations::VIVO_CONTROLE), $message);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $service->serviceTransaction,
            'services.status' => ServiceStatus::ACCEPTED
        ], 'mongodb');
    }

    /** @test */
    public function should_reject_service_when_credit_analysis_errors_vivobr()
    {
        $user = UserBuilder::make()->build();
        $user->update([
            'cpf' => SunTestBook::FALIURE_CREDITY_ANALYSIS
        ]);

        $service = $this->buildSale($user);

        $received = $this->assistance()->integrateService($service);

        $expectedMessage = 'Falha na análise de crédito. A venda foi finalizada como desistência';
        $receivedMessage = data_get($received->getOriginalContent(), 'message');

        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $received->status());
        $this->assertEquals($expectedMessage, $receivedMessage);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $service->serviceTransaction,
            'services.status' => ServiceStatus::REJECTED
        ], 'mongodb');
    }

    /** @test */
    public function should_not_reject_service_when_unrecognized_error_code_vivobr()
    {
        $user = UserBuilder::make()->build();
        $user->update([
            'cpf' => SunTestBook::FAILURE_SALESMAN_NOT_FOUND
        ]);

        $service = $this->buildSale($user);

        $received = $this->assistance()->integrateService($service);

        $expectedMessage = 'Vendedor não cadastrado no sistema SUN.';
        $receivedMessage = data_get($received->getOriginalContent(), 'message');

        $this->assertEquals(Response::HTTP_PRECONDITION_FAILED, $received->status());
        $this->assertEquals($expectedMessage, $receivedMessage);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $service->serviceTransaction,
            'services.status' => ServiceStatus::PENDING_SUBMISSION
        ], 'mongodb');
    }

    private function buildSale(User $user = null): Service
    {
        $user        = $user ?? UserBuilder::make()->build();
        $network     = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $service     = $this->sunFactories()->of(VivoControle::class)->make();

        return SaleBuilder::make()
            ->withServices([$service])
            ->withPointOfSale($pointOfSale)
            ->withUser($user)
            ->build()
            ->services
            ->first();
    }

    private function assistance(): VivoBRControleAssistance
    {
        return resolve(VivoBRControleAssistance::class);
    }
}

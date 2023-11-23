<?php

namespace Uol\Tests\Feature;

use Gateway\Services\GatewayService;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Uol\Models\UolCurso;
use Uol\Services\UolPassaporteService;

class UolCancelFeatureTest extends TestCase
{
    use AuthHelper;

    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/uol/Factories'));
    }

    /** @test */
    public function put_should_return_422_when_service_not_active()
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $uolCurso = $this->factory->of(UolCurso::class)->make();

        (new SaleBuilder())->withServices([$uolCurso])->build();

        $response = $this->authAs($user)->put('sales/cancel', [
            'serviceTransaction' => $uolCurso->serviceTransaction
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'message' => trans('exceptions.service.' . ServiceExceptions::ACTIVE_TO_CANCEL)
        ]);
    }

    /** @test */
    public function put_should_return_422_when_cancellation_expired()
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $uolCurso = $this->factory->of(UolCurso::class)->make([
            'status' => ServiceStatus::APPROVED
        ]);

        $sale = (new SaleBuilder())->withServices([$uolCurso])->build();
        $sale->forceFill(['createdAt' => now()->subMonth()])->save();

        $response = $this->authAs($user)->put('sales/cancel', [
                'serviceTransaction' => $uolCurso->serviceTransaction
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'message' => trans('exceptions.service.' . ServiceExceptions::CANCELLATION_EXPIRED)
        ]);
    }

    /** @test */
    public function put_should_cancel_transaction_in_gateway_when_paymentStatus_is_approved()
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $uolCurso = $this->factory->of(UolCurso::class)->make([
            'status'  => ServiceStatus::APPROVED,
            'payment' => [
                'status' => ServiceStatus::APPROVED
            ]
        ]);

        (new SaleBuilder())->withServices([$uolCurso])->build();

        $gateway = \Mockery::mock(GatewayService::class)->makePartial();
        $gateway->shouldReceive('cancel')->once();
        app()->instance(GatewayService::class, $gateway);

        $response = $this->authAs($user)->put('sales/cancel', [
                'serviceTransaction' => $uolCurso->serviceTransaction
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'message' => trans('uol::messages.passport_canceled')
        ]);
    }

    /** @test */
    public function put_should_return_200_when_cancel_service()
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $uolCurso = $this->factory->of(UolCurso::class)->make([
            'status'               => ServiceStatus::APPROVED,
            'payment' => [
                'gatewayTransactionId' => '123',
                'status' => ServiceStatus::APPROVED,
            ],
            'operatorIdentifiers' => [
                'passportSerie' => '123',
                'passportNumber' => '123'

            ]
        ]);

        (new SaleBuilder())->withServices([$uolCurso])->build();

        $response = $this->authAs($user)->put('sales/cancel', [
                'serviceTransaction' => $uolCurso->serviceTransaction
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'message' => trans('uol::messages.passport_canceled')
        ]);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $uolCurso->serviceTransaction,
            'services.status' => ServiceStatus::CANCELED,
            'services.payment.status' => ServiceStatus::CANCELED
        ], 'mongodb');
    }

    /** @test */
    public function put_should_cancel_in_client_when_exists_serie()
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);

        $user     = (new UserBuilder())->withPermission($permission)->build();
        $uolCurso = $this->factory->of(UolCurso::class)->make([
            'status'        => ServiceStatus::APPROVED,
            'operatorIdentifiers' => [
                'passportSerie' => '123',
                'passportNumber' => '123'

            ]
        ]);

        (new SaleBuilder())->withServices([$uolCurso])->build();

        $uol = \Mockery::mock(UolPassaporteService::class)->makePartial();
        $uol->shouldReceive('cancel')->once();
        app()->instance(UolPassaporteService::class, $uol);

        $response = $this->authAs($user)->put('sales/cancel', [
            'serviceTransaction' => $uolCurso->serviceTransaction
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'message' => trans('uol::messages.passport_canceled')
        ]);
    }
}

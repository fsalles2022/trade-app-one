<?php


namespace Generali\tests\Feature;

use Generali\Assistance\Connection\GeneraliRoutes;
use Generali\Models\Generali;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class GeneraliCancelFeatureTest
{
    use AuthHelper;

    /** @test */
    public function should_return_200_with_refund_price(): void
    {
        $user = (new UserBuilder())->build();
        $this->authAs($user)
            ->get('generali/v1/refund/?reference=202001101630552202-0')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'refund'
            ]);
    }

    /** @test */
    public function should_return_200_when_service_was_cancelled(): void
    {
        $user = (new UserBuilder())
            ->withPermission(SalePermission::getFullName(SalePermission::CANCEL))
            ->build();

        $service = factory(Generali::class)->create(['status' => ServiceStatus::APPROVED]);

        (new SaleBuilder())->withServices([$service])->build();

        $this->authAs($user)
            ->put('sales/cancel', ['serviceTransaction' => $service->serviceTransaction])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'message' => trans('generali::messages.service_cancelled')
            ]);

        $this->assertDatabaseMissing('sales', [
            'serviceTransaction' => $service->serviceTransaction,
            'status' => ServiceStatus::CANCELED,
            'payment.status' => ServiceStatus::CANCELED
        ], 'mongodb');
    }

    /** @test */
    public function should_return_422_when_generali_service_is_not_active(): void
    {
        $permission = SalePermission::getFullName(SalePermission::CANCEL);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $service    = factory(Generali::class)->create();

        (new SaleBuilder())->withServices([$service])->build();

        $this->authAs($user)->put('sales/cancel', ['serviceTransaction' => $service->serviceTransaction])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment(['message' => trans('exceptions.service.' . ServiceExceptions::ACTIVE_TO_CANCEL)]);
    }
}

<?php


namespace TradeAppOne\Tests\Feature;

use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SaleEditStatusTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_update_sale_status()
    {
        $permissions = factory(Permission::class)->create([
            'slug'   => 'SALE.EDIT_STATUS'
        ]);

        $user    = (new UserBuilder())->withPermissions([$permissions])->build();
        $hierarchy   = (new HierarchyBuilder())->withUser($user)->build();
        (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $sale      = (new SaleBuilder())->withServices([ClaroServices::ControleBoleto()])->build();
        $serviceId = $sale->services->first()->serviceTransaction;

        $response = $this->authAs($user)
            ->post('/service/edit/status', [
                'status' => ServiceStatus::APPROVED,
                'serviceTransaction' => $serviceId,
            ]);

        $saleUpdated = Sale::query()->where('services.serviceTransaction', $serviceId)->first();

        $this->assertEquals($saleUpdated->services->first()->status, ServiceStatus::APPROVED);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}

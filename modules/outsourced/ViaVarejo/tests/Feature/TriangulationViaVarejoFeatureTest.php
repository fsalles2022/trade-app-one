<?php


namespace Outsourced\ViaVarejo\tests\Feature;

use Discount\Models\Discount;
use Discount\Models\DiscountProduct;
use Discount\Tests\Helpers\Builders\DiscountBuilder;
use Illuminate\Http\Response;
use Outsourced\ViaVarejo\Models\ViaVarejoCoupon;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TriangulationViaVarejoFeatureTest extends TestCase
{
    use AuthHelper;

    private const ROUTE = '/coupons/triangulation';

    /** @test */
    public function should_return_coupon_via_varejo_by_params(): void
    {
        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $product = factory(DiscountProduct::class)->make([
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_CONTROLE_FACIL
        ]);

        (new DiscountBuilder())->withNetwork($network)->withUser($user)->withProduct($product)->build();
        $this->buildCoupon();
        $discount = Discount::with('devices.device', 'products')->first();

        $plan        = $discount->products->first()->product;
        $sku         = $discount->devices->first()->device->sku;
        $queryString = sprintf('?sku=%s&plan=%s', $sku, $plan);

        $this->authAs($user)
            ->get(self::ROUTE . $queryString)
            ->assertJsonStructure(['coupon'])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_not_found_when_not_have_coupon(): void
    {
        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
        $user    = (new UserBuilder())->withNetwork($network)->build();

        $this->authAs($user)
            ->get(self::ROUTE)
            ->assertJsonStructure([
                'shortMessage',
                'message',
                'description',
                'help',
                'transportedMessage',
                'transportedData'
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    private function buildCoupon(): void
    {
        ViaVarejoCoupon::create(['discountId' => Discount::all()->first()->id, 'campaign' => '120130', 'coupon' => 'ABC123PF']);
    }
}

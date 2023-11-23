<?php


namespace TradeAppOne\Tests\Unit\Domain\Exportable;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\PointOfSalePermission;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PointOfSaleExportTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_403_when_user_has_no_authorize_to_export_pos(): void
    {
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->withNetwork($network)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $this->authAs($user)
            ->json('GET', '/management/points_of_sale/export', ['networks' => [$network->slug]])
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonFragment(['message' => trans('exceptions.user.' . UserExceptions::UNAUTHORIZED)]);
    }

    /** @test */
    public function should_return_all_pos_when_user_has_authorize_and_filtered(): void
    {
        $permission  = PointOfSalePermission::getFullName(PermissionActions::EXPORT);
        $network     = (new NetworkBuilder())->build();
        $hierarchy   = (new HierarchyBuilder())->withNetwork($network)->build();
        $pos         = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->withNetwork($network)->build();
        $user        = (new UserBuilder())->withPointOfSale($pos)->withPermission($permission)->withHierarchy($hierarchy)->build();

        $this->authAs($user)
            ->json('GET', '/management/points_of_sale/export', ['cnpj' => $pos->cnpj])
            ->assertStatus(Response::HTTP_OK)
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
<?php


namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\RefusedSaleReportPermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RefusedSaleReportFeatureTest extends TestCase
{
    use AuthHelper;

    private const ROUTE_REPORT = 'reports/refused';
    private const ROUTE_EXPORT = 'reports/refused/export';
    private const REQUEST_PAGE = 2;
    private $user;

    protected function setUp()
    {
        parent::setUp();
        $permissionView   = factory(Permission::class)->create([
            'slug' => RefusedSaleReportPermission::getFullName(RefusedSaleReportPermission::VIEW),
            'client' => SubSystemEnum::WEB
        ]);
        $permissionExport = factory(Permission::class)->create([
            'slug' => RefusedSaleReportPermission::getFullName(RefusedSaleReportPermission::EXPORT),
            'client' => SubSystemEnum::WEB
        ]);
        $this->user       = (new UserBuilder())->withPermissions([$permissionView, $permissionExport])->build();
    }


    /** @test */
    public function get_should_return_paginated_records()
    {
        $response = $this->authAs($this->user)
            ->get(self::ROUTE_REPORT);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'current_page', 'data', 'total', 'from', 'total', 'per_page'
        ]);
    }

    /** @test */
    public function get_should_return_paginated_records_of_page()
    {
        $response = $this->authAs($this->user)
            ->get(self::ROUTE_REPORT . '?page=' . self::REQUEST_PAGE);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'current_page', 'data', 'total', 'from', 'total', 'per_page'
        ]);
        $response->assertJsonFragment(['current_page' => self::REQUEST_PAGE]);
    }

    /** @test */
    public function get_export_should_return_records_as_attachment_csv()
    {
        $response = $this->authAs($this->user)
            ->get(self::ROUTE_EXPORT);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=relatorio_negados.csv');
    }
}

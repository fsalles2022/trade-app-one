<?php

namespace ClaroBR\Tests\Unit\OperationAssistants;

use ClaroBR\Models\ClaroPre;
use ClaroBR\OperationAssistances\ClaroPreAssistant;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\Helpers\SivIntegrationHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroPreAssistantTest extends TestCase
{
    use SivIntegrationHelper, SivFactoriesHelper, SivBindingHelper;

    /** @test */
    public function should_return_true_when_service_response_return_with_protocol()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ClaroPre::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(ClaroPreAssistant::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->will(
                $this->returnValue(
                    ['type' => 'success', 'data' => ['protocol' => '2018506496871', 'status' => 'SUCESSO']]
                )
            );
        self::assertTrue($sivSaleAssistance->checkSaleIsActivatedByPayload($rest));
    }

    public function bindIncompleteRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService'])
                ->getMock();

            $service                      = $saleEntity->services()->first();
            $service->operatorIdentifiers = [];

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            return $repository;
        });
    }

    /** @test */
    public function should_return_false_when_service_response_dont_return_with_protocol()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ClaroPre::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(ClaroPreAssistant::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->will(
                $this->returnValue(
                    ['type' => 'success', 'data' => ['msisdns' => []]]
                )
            );
        self::assertFalse($sivSaleAssistance->checkSaleIsActivatedByPayload($rest));
    }

    /** @test */
    public function should_return_message_activation_when_is_success()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ClaroPre::class)
            ->make(['status' => 'SUBMITTED'])
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $this->bindSivResponse();
        $sivSaleAssistance = app()->make(ClaroPreAssistant::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->willReturn(['type' => 'success', 'data' => ['msisdns' => []]]);
        $result   = $sivSaleAssistance->activate($saleEntity->services()->first());
        $response = json_decode($result->getContent(), true);
        self::assertArrayHasKey('message', $response);
        self::assertEquals(trans('siv::messages.activation.claro_pre.message'), $response['message']);
    }
}

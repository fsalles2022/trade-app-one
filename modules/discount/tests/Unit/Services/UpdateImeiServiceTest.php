<?php

declare(strict_types=1);

namespace Discount\Tests\Unit\Services;

use Carbon\Carbon;
use Discount\Listeners\ImeiUpdateLogGenerator;
use Discount\Models\ImeiChangeHistory;
use Discount\Repositories\ImeiChangeHistoryRepository;
use Discount\Services\Input\AuthorizationUpdateImeiInput;
use Discount\Services\Input\GetSaleWithImeiInput;
use Discount\Services\Input\UpdateImeiServiceInput;
use Discount\Services\Output\GetAuthorizationImeiOutput;
use Discount\Services\Output\GetSaleWithimeiList0utput;
use Discount\Services\Output\GetSaleWithImeiOutput;
use Discount\Services\Output\Output;
use Discount\Services\Output\UpdateImeiServiceOutput;
use Discount\Services\UpdateImeiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UpdateImeiServiceTest extends TestCase
{
    /** @var UpdateImeiService */
    private $updateImeiService;

    /** @var SaleService|MockInterface */
    private $saleServiceMocked;

    /** @var UserService|MockInterface */
    private $userServiceMocked;

    /** @var ImeiChangeHistoryRepository|MockInterface */
    private $imeiChangeHistoryRepositoryMocked;

    protected function setUp(): void
    {
        parent::setUp();
        $this->updateImeiService = $this->getInstanceMocked();
        $this->mockImeiUpdateLogGenerator();
    }

    public function test_should_return_an_output_empty_when_not_parameters(): void
    {
        $output = $this->updateImeiService->getInformationAboutSale(
            new GetSaleWithImeiInput(null, null)
        );

        $this->assertInstanceOf(Output::class, $output);
        $this->assertArrayHasKey('serviceTransaction', $output->jsonSerialize());
        $this->assertArrayHasKey('customer', $output->jsonSerialize());
        $this->assertArrayHasKey('buyDate', $output->jsonSerialize());
        $this->assertArrayHasKey('imei', $output->jsonSerialize());
        $this->assertNull($output->jsonSerialize()['serviceTransaction']);
        $this->assertNull($output->jsonSerialize()['customer']['cpf']);
        $this->assertNull($output->jsonSerialize()['buyDate']);
        $this->assertNull($output->jsonSerialize()['imei']);
    }

    public function test_should_return_an_output_hydrated(): void
    {
        $serviceTransaction = '909090909090-1';

        $service = $this->populateService();

        $this->saleServiceMocked
            ->shouldReceive('findService')
            ->once()
            ->withAnyArgs()
            ->andReturn($service);

        $output = $this->updateImeiService->getInformationAboutSale(
            new GetSaleWithImeiInput(null, $serviceTransaction)
        );

        $this->assertInstanceOf(Output::class, $output);
        $this->assertInstanceOf(GetSaleWithImeiOutput::class, $output);
        $this->assertSame($output->getServiceTransaction(), $service->serviceTransaction);
        $this->assertSame($output->getCustomerFirstName(), $service->customer['firstName']);
        $this->assertSame($output->getCustomerLastName(), $service->customer['lastName']);
        $this->assertSame($output->getCustomerCpf(), $service->customer['cpf']);
        $this->assertSame($output->getImei(), $service->imei);
    }

    public function test_should_return_an_collection_of_outputs(): void
    {
        $service = $this->populateService();
        $sales   = collect()->push($this->populateSale($service))->push($this->populateSale($service));

        $this->saleServiceMocked
            ->shouldReceive('getSalesByCustomerCpf')
            ->once()
            ->withAnyArgs()
            ->andReturn($sales);

        $output = $this->updateImeiService->getInformationAboutSale(
            new GetSaleWithImeiInput('3434253243', null)
        );

        $this->assertInstanceOf(Output::class, $output);
        $this->assertInstanceOf(GetSaleWithimeiList0utput::class, $output);
        $this->assertInstanceOf(GetSaleWithImeiOutput::class, current($output->getGetSaleWithImeiOutput()));
        $this->assertSame($service->serviceTransaction, $output->getGetSaleWithImeiOutput()[0]->getServiceTransaction());
        $this->assertSame($service->imei, $output->getGetSaleWithImeiOutput()[0]->getImei());
        $this->assertSame($service->customer['cpf'], $output->getGetSaleWithImeiOutput()[0]->getCustomerCpf());
        $this->assertSame($service->customer['firstName'], $output->getGetSaleWithImeiOutput()[0]->getCustomerFirstName());
        $this->assertSame($service->customer['lastName'], $output->getGetSaleWithImeiOutput()[0]->getCustomerLastName());
    }

    public function test_should_thrown_exception_unauthorized_when_user_not_found(): void
    {
        $updateImeiService = $this->getInstanceMocked();
        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->once()
            ->withAnyArgs()
            ->andReturn(null);

        $this->expectException(BuildExceptions::class);

        $updateImeiService->authorize(
            new AuthorizationUpdateImeiInput(
                'test@tradeupgroup.com',
                '12345',
                '2134252143214-0'
            )
        );
    }

    public function test_should_thrown_exception_unauthorized_when_user_not_has_permission(): void
    {
        $updateImeiService = $this->getInstanceMocked();

        $mock = \Mockery::mock(User::class);

        $mock->shouldReceive('hasPermission')
            ->once()
            ->withAnyArgs()
            ->andReturnFalse();

        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->once()
            ->withAnyArgs()
            ->andReturn($mock);

        $this->expectException(BuildExceptions::class);

        $updateImeiService->authorize(
            new AuthorizationUpdateImeiInput(
                'test@tradeupgroup.com',
                '12345',
                '2134252143214-0'
            )
        );
    }

    public function test_should_thrown_exception_unauthorized_when_hash_incorrect(): void
    {
        $updateImeiService = $this->getInstanceMocked();

        $user = resolve(User::class);
        $user->setAttribute('password', 'none');

        $mock = \Mockery::mock($user);

        Hash::shouldReceive('check')->once()->withAnyArgs()->andReturnFalse();

        $mock->shouldReceive('hasPermission')
            ->once()
            ->withAnyArgs()
            ->andReturnTrue();

        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->once()
            ->withAnyArgs()
            ->andReturn($mock);

        $this->expectException(BuildExceptions::class);

        $updateImeiService->authorize(
            new AuthorizationUpdateImeiInput(
                'test@tradeupgroup.com',
                '12345',
                '2134252143214-0'
            )
        );
    }

    public function test_should_return_output_with_token(): void
    {
        $updateImeiService = $this->getInstanceMocked();

        $user = resolve(User::class);
        $user->setAttribute('password', 'none');

        $mock = \Mockery::mock($user);

        Hash::shouldReceive('check')->once()->withAnyArgs()->andReturnTrue();

        $mock->shouldReceive('hasPermission')
            ->once()
            ->withAnyArgs()
            ->andReturnTrue();

        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->once()
            ->withAnyArgs()
            ->andReturn($mock);

        $output = $updateImeiService->authorize(
            new AuthorizationUpdateImeiInput(
                'test@tradeupgroup.com',
                '12345',
                '2134252143214-0'
            )
        );

        $this->assertInstanceOf(Output::class, $output);
        $this->assertInstanceOf(GetAuthorizationImeiOutput::class, $output);
        $this->assertNotNull($output->getHash());
    }

    public function test_should_thrown_exception_unauthorized_when_try_update(): void
    {
        Cache::shouldReceive('get')->once()->andReturnNull();

        $this->expectException(BuildExceptions::class);

        $this->updateImeiService->updateImeiInService(new UpdateImeiServiceInput(
            '112332545345',
            '324123434',
            '98735598473584-0',
            '543254',
            '57432854',
            '24324324432'
        ));
    }

    public function test_should_return_an_output_when_update_imei(): void
    {
        $this->loginWithUser();

        $newImei = '543254';

        Cache::shouldReceive('get')->once()->andReturn('DSJFHDFFAJD734243');

        $this->saleServiceMocked
            ->shouldReceive('findService')
            ->withAnyArgs()
            ->once()
            ->andReturn($this->populateService());

        $serviceStub = $this->populateService()
            ->setAttribute('imei', $newImei)
            ->setAttribute('user', ['id' => 1, 'cpf' => '34321423542']);

        $this->saleServiceMocked
            ->shouldReceive('updateImei')
            ->withAnyArgs()
            ->once()
            ->andReturn($serviceStub);

        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->withAnyArgs()
            ->once()
            ->andReturn($this->populateUser());

        $this->imeiChangeHistoryRepositoryMocked
            ->shouldReceive('save')
            ->withAnyArgs()
            ->once()
            ->andReturn(new ImeiChangeHistory());

        $output = $this->updateImeiService->updateImeiInService(new UpdateImeiServiceInput(
            'DSJFHDFFAJD734243',
            '324123434',
            '98735598473584-0',
            $newImei,
            '4372418234',
            '24324324432'
        ));

        $this->assertInstanceOf(Output::class, $output);
        $this->assertInstanceOf(UpdateImeiServiceOutput::class, $output);
        $this->assertTrue($output->isSuccess());
    }

    public function test_should_return_false_when_not_update_imei(): void
    {
        $this->loginWithUser();

        $newImei = '543254';

        Cache::shouldReceive('get')->once()->andReturn('DSJFHDFFAJD734243');

        $this->saleServiceMocked
            ->shouldReceive('findService')
            ->withAnyArgs()
            ->once()
            ->andReturn($this->populateService());

        $serviceStub = $this->populateService()
            ->setAttribute('imei', '34124234423')
            ->setAttribute('user', ['id' => 1, 'cpf' => '34321423542']);

        $this->saleServiceMocked
            ->shouldReceive('updateImei')
            ->withAnyArgs()
            ->once()
            ->andReturn($serviceStub);

        $this->userServiceMocked
            ->shouldReceive('getUserByCpf')
            ->withAnyArgs()
            ->once()
            ->andReturn($this->populateUser());

        $this->imeiChangeHistoryRepositoryMocked
            ->shouldReceive('save')
            ->withAnyArgs()
            ->once()
            ->andReturn(new ImeiChangeHistory());

        $output = $this->updateImeiService->updateImeiInService(new UpdateImeiServiceInput(
            'DSJFHDFFAJD734243',
            '324123434',
            '98735598473584-0',
            $newImei,
            '4231434234',
            '24324324432'
        ));

        $this->assertInstanceOf(Output::class, $output);
        $this->assertInstanceOf(UpdateImeiServiceOutput::class, $output);
        $this->assertFalse($output->isSuccess());
    }

    private function getInstanceMocked(): UpdateImeiService
    {
        $this->saleServiceMocked = \Mockery::mock(SaleService::class);
        $this->userServiceMocked = \Mockery::mock(UserService::class);

        $this->app->bind(UpdateImeiService::class, function (): UpdateImeiService {
            return new UpdateImeiService($this->userServiceMocked, $this->saleServiceMocked);
        });

        return resolve(UpdateImeiService::class);
    }

    private function mockImeiUpdateLogGenerator(): void
    {
        $this->imeiChangeHistoryRepositoryMocked = \Mockery::mock(ImeiChangeHistoryRepository::class);
        $this->app->bind(ImeiUpdateLogGenerator::class, function (): ImeiUpdateLogGenerator {
            return new ImeiUpdateLogGenerator(
                $this->imeiChangeHistoryRepositoryMocked,
                $this->userServiceMocked
            );
        });
    }

    private function populateService(): Service
    {
        return new Service([
            'serviceTransaction' => '909090909090-1',
            'customer' => [
                'cpf' => '8483758434310',
                'firstName' => 'Kelvin',
                'lastName' => 'Silva'
            ],
            'imei' => '9834583288',
        ]);
    }

    private function populateSale(Service $service): Sale
    {
        $sale = new Sale();
        $sale->services()->associate($service);
        $sale->createdAt = new Carbon();
        return $sale;
    }

    private function populateUser()
    {
        return factory(User::class)->make();
    }

    private function loginWithUser(): void
    {
        $user = UserBuilder::make()->build();
        Auth::login($user);
    }
}

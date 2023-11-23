<?php

declare(strict_types=1);

namespace Discount\Tests\Feature;

use Discount\Enumerators\ImeiEnum;
use Discount\Exceptions\ImeiExceptions;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImeiUpdateTest extends TestCase
{
    use AuthHelper;

    protected const ROUTE = '/imei';

    protected function setUp(): void
    {
        parent::setUp();
        $this->authAs();
    }

    public function test_should_thrown_exception_sale_not_found(): void
    {
        $this->get(self::ROUTE . '?serviceTransaction=90909090900-0')
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJsonMissing(['error' => ['shortMessage' => 'SaleNotFound']]);
    }

    public function test_should_get_imei_by_service_transaction(): void
    {
        $imei               = '9090909090';
        $service            = factory(Service::class)->create(['imei' => $imei]);
        $sale               = SaleBuilder::make()->withServices($service)->build();
        $serviceTransaction = $sale->services->first()->serviceTransaction;

        $this->get(self::ROUTE . '?serviceTransaction=' . $serviceTransaction)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'serviceTransaction' => $serviceTransaction,
                'customer' => [
                    'cpf' => $service->customer['cpf'] ?? null,
                    'name' => $service->customer['firstName'] . ' ' . $service->customer['lastName']
                ],
                'imei' => $imei,
                'buyDate' => null
            ]);
    }

    public function test_should_return_an_sales_data_collection(): void
    {
        $customerCpf = '79955495804';
        $imei        = '234234234234';

        $service = factory(Service::class)->states('updateImei')->create([
            'customer' => ['cpf' => $customerCpf],
            'imei' => $imei
        ]);
        $sale    = SaleBuilder::make()->withServices($service)->build();
        $this->get(self::ROUTE . '?cpf=' . $customerCpf)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMissing(
                [
                    'sales' => [
                        [
                            'serviceTransaction' => $service->serviceTransaction,
                            'customer' => [
                                'cpf' => $service->customer['cpf'] ?? null,
                            ],
                            'imei' => $service->imei,
                            'buyDate' => $sale->createdAt->format('Y-m-d')
                        ]
                    ]
                ]
            );
    }

    public function test_thrown_exception_when_get_imei_by_cpf_customer_and_not_found_sale(): void
    {
        $this->get(self::ROUTE . '?cpf=79955495804')
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMissing(['error' => ['shortMessage' => 'SaleNotFoundByCustomer']]);
    }

    public function test_should_throw_exception_when_not_has_permission(): void
    {
        $userCpf     = '79955495804';
        $password    = 'Trade2022';
        $customerCpf = '79955495804';
        $imei        = '234234234234';

        /** @var User $user */
        $user    = factory(User::class)->create(['cpf' => $userCpf, 'password' => $password]);
        $service = factory(Service::class)->states('updateImei')->create([
            'customer' => ['cpf' => $customerCpf],
            'imei' => $imei
        ]);

        SaleBuilder::make()->withServices($service)->build();

        $this->post(
            self::ROUTE . '/authorization',
            ['login' => $user->cpf, 'password' => $user->password, 'serviceTransaction' => $service->serviceTransaction]
        )
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonFragment([
                'shortMessage' => ImeiExceptions::unauthorized()->getShortMessage(),
                'message' => ImeiExceptions::unauthorized()->getMessage()
            ]);
    }

    public function test_should_thrown_un_authorized_when_user_not_exists(): void
    {
        $this->post(
            self::ROUTE . '/authorization',
            ['login' => '79955495804', 'password' => 'Trade2021', 'serviceTransaction' => '994385843844-0']
        )
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonFragment([
                'shortMessage' => ImeiExceptions::unauthorized()->getShortMessage(),
                'message' => ImeiExceptions::unauthorized()->getMessage()
            ]);
    }

    public function test_should_throw_un_authorized_when_sale_not_exists(): void
    {
        $userCpf          = '79955495804';
        $password         = 'Trade2022';
        $serviceNotExists = '3424348573458-0';
        $user             = factory(User::class)->create(['cpf' => $userCpf, 'password' => $password]);

        $this->post(
            self::ROUTE . '/authorization',
            ['login' => $user->cpf, 'password' => $user->password, 'serviceTransaction' => $serviceNotExists]
        )
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJsonFragment([
                'shortMessage' => ImeiExceptions::unauthorized()->getShortMessage(),
                'message' => ImeiExceptions::unauthorized()->getMessage()
            ]);
    }

    public function test_should_get_token_when_user_has_permission_and_authorized(): void
    {
        $password    = 'Trade2022';
        $customerCpf = '79955495804';
        $imei        = '234234234234';

        $permission = factory(Permission::class)
            ->create(['slug' => ImeiEnum::PERMISSION . '.' . PermissionActions::EDIT]);

        $user           = (new UserBuilder())->withPermissions([$permission])->build();
        $user->password = bcrypt($password);
        $user->save();

        $service = factory(Service::class)->states('updateImei')->create([
            'customer' => ['cpf' => $customerCpf],
            'imei' => $imei
        ]);

        SaleBuilder::make()->withServices($service)->build();

         $this->post(
             self::ROUTE . '/authorization',
             ['login' => $user->cpf, 'password' => $password, 'serviceTransaction' => $service->serviceTransaction]
         )
         ->assertSuccessful()
         ->assertJsonMissing(['token' => 'test']);
    }

    public function test_should_thrown_un_authorized_when_hash_invalid(): void
    {
        $authorizerCpf = '79955495804';
        $customerCpf   = '36304643861';
        $hashMocked    = '47365JFDSHF832423DFAS';

        $this->put(
            self::ROUTE,
            [
                'token' => $hashMocked,
                'newImei' => '8453454',
                'oldImei' => '12321334',
                'authorizerCpf' => $authorizerCpf,
                'serviceTransaction' => '8473465236454-0',
                'customerCpf' => $customerCpf
            ]
        )
        ->assertStatus(Response::HTTP_FORBIDDEN)
        ->assertJsonFragment([
            'shortMessage' => ImeiExceptions::unauthorized()->getShortMessage(),
            'message' => ImeiExceptions::unauthorized()->getMessage()
        ]);
    }

    public function test_should_update_imei_in_service(): void
    {
        $authorizerCpf = '79955495804';
        $customerCpf   = '36304643861';
        $hashMocked    = '47365JFDSHF832423DFAS';
        $imei          = '234234234234';

        factory(User::class)->create(['cpf' => '79955495804']);

        $service = factory(Service::class)->states('updateImei')->create([
            'customer' => ['cpf' => $customerCpf],
            'imei' => $imei,
            'user' => [
                'id' => '1',
                'cpf' => '324475732574'
            ]
        ]);

        SaleBuilder::make()->withServices($service)->build();

        Cache::shouldReceive('get')
            ->once()
            ->andReturn($hashMocked);

        $this->put(
            self::ROUTE,
            [
                'token' => $hashMocked,
                'newImei' => '8453454',
                'oldImei' => '213213',
                'authorizerCpf' => $authorizerCpf,
                'serviceTransaction' => $service->serviceTransaction,
                'customerCpf' => $customerCpf
            ]
        )
            ->assertSuccessful()
            ->assertJsonFragment(['success' => true]);
    }
}

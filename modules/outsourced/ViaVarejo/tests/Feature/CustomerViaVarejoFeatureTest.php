<?php
declare(strict_types=1);

namespace Outsourced\ViaVarejo\tests\Feature;

use Illuminate\Http\Response;
use Outsourced\ViaVarejo\tests\ViaVarejoTestBook;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class CustomerViaVarejoFeatureTest extends TestCase
{
    use AuthHelper;
    private const ROUTE = '/customer/validate/';

    /** @test */
    public function should_return_successfully_cpf_validate_via_varejo(): void
    {
        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
        $user    = (new UserBuilder())->withNetwork($network)->build();
        $url     = self::ROUTE . ViaVarejoTestBook::CPF_SHOULD_RETURN_200;

        $this->authAs($user)
            ->get($url)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => trans('via_varejo::messages.ViaVarejoCustomerFound')]);
    }

//    /** @test */
//    public function should_return_not_found_cpf_validate_via_varejo(): void
//    {
//        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
//        $user    = (new UserBuilder())->withNetwork($network)->build();
//        $url     = self::ROUTE . ViaVarejoTestBook::CPF_SHOULD_RETURN_404;
//
//        $this->authAs($user)
//            ->get($url)
//            ->assertStatus(Response::HTTP_NOT_FOUND)
//            ->assertJson(ViaVarejoTestBook::RESPONSE_USER_NOT_FOUND);
//    }
//
//    /** @test */
//    public function should_return_successfully_without_cpf_in_url(): void
//    {
//        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
//        $user    = (new UserBuilder())->withNetwork($network)->build();
//        $url     = self::ROUTE;
//
//        $this->authAs($user)
//            ->get($url)
//            ->assertStatus(Response::HTTP_OK)
//            ->assertJson([]);
//    }
//
//    /** @test */
//    public function should_return_service_not_allowed(): void
//    {
//        $network = factory(Network::class)->create(['slug' => NetworkEnum::GPA]);
//        $user    = (new UserBuilder())->withNetwork($network)->build();
//        $url     = self::ROUTE . ViaVarejoTestBook::CPF_SHOULD_RETURN_200;
//
//            $this->authAs($user)
//                ->get($url)
//                ->assertStatus(Response::HTTP_PRECONDITION_FAILED)
//                ->assertJson(ViaVarejoTestBook::RESPONSE_NOT_ALOWED);
//    }
//
//    /** @test */
//    public function should_return_service_not_available(): void
//    {
//        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
//        $user    = (new UserBuilder())->withNetwork($network)->build();
//        $url     = self::ROUTE . ViaVarejoTestBook::CPF_SHOULD_RETURN_422;
//
//        $this->authAs($user)
//            ->get($url)
//            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
//            ->assertJson(ViaVarejoTestBook::RESPONSE_UNPROCESSABLE_ENTITY);
//    }
}

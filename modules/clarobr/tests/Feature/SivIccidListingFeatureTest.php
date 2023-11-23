<?php


namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\ClaroBRTestBook;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SivIccidListingFeatureTest extends TestCase
{
    use AuthHelper, SivBindingHelper;

    /** @test */
    public function should_return_list_of_iccids(): void
    {
        $this->bindSivResponse();
        $operator = Operator::create([
            'slug' => Operations::CLARO
        ]);
        $channel  = factory(Channel::class)->states(Channels::DISTRIBUICAO)->create();
        $role     = factory(Role::class)->create([
            'slug' => 'vendedor-promotor-inova'
        ]);
        $user     = (new UserBuilder())->withOperators($operator)->withUserChannel($channel)->withRole($role)->build();
        $this->authAs($user)
            ->get('/siv/' . ClaroBRTestBook::ICCID_PREFIX_WITH_SIMCARD . '/iccid')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function should_return_no_iccids_when_wrong_prefix(): void
    {
        $this->bindSivResponse();
        $operator = Operator::create([
            'slug' => Operations::CLARO
        ]);
        $channel  = factory(Channel::class)->states(Channels::DISTRIBUICAO)->create();
        $role     = factory(Role::class)->create([
            'slug' => 'vendedor-promotor-inova'
        ]);
        $user     = (new UserBuilder())->withOperators($operator)->withUserChannel($channel)->withRole($role)->build();
        $this->authAs($user)
            ->get('/siv/000000/iccid')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function should_return_400_when_prefix_too_short(): void
    {
        $this->bindSivResponse();
        $operator = Operator::create([
            'slug' => Operations::CLARO
        ]);
        $channel  = factory(Channel::class)->states(Channels::DISTRIBUICAO)->create();
        $role     = factory(Role::class)->create([
            'slug' => 'vendedor-promotor-inova'
        ]);
        $user     = (new UserBuilder())->withOperators($operator)->withUserChannel($channel)->withRole($role)->build();
        $this->authAs($user)
            ->get('/siv/00000/iccid')
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function should_return_412_when_user_not_promoter(): void
    {
        $this->bindSivResponse();
        $user = (new UserBuilder())->build();
        $this->authAs($user)
            ->get('/siv/000000/iccid')
            ->assertStatus(Response::HTTP_PRECONDITION_FAILED);
    }
}

<?php

declare(strict_types=1);

namespace Terms\Feature;

use Illuminate\Foundation\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Terms\Enums\StatusUserTermsEnum;
use Terms\Enums\TypeTermsEnum;
use Terms\Models\Term;
use Terms\Models\UserTerm;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class TermFeatureTest extends TestCase
{
    use AuthHelper;

    private const ROUTE_TERM_USE    = '/terms/use?type=';
    private const ROUTE_TERM_ACCEPT = '/terms/use/accept';

    /** @return array[] */
    public function parameters(): array
    {
        return [
            [StatusUserTermsEnum::VIEWED, TypeTermsEnum::SALESMAN],
            [StatusUserTermsEnum::VIEWED, TypeTermsEnum::CUSTOMER],
        ];
    }

    /** @dataProvider parameters */
    public function test_should_return_term_of_salesman_and_customer(string $status, string $type): void
    {
        $term = $this->createTermWithState($type);
        $this->getResponse($type, null)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                [
                    'term' =>  $term->toArray(),
                    'userStatus' => $status
                ]
            );
    }

    public function test_should_return_an_empty_array_inactive_term(): void
    {
        $this->createTermWithState('inactive');
        $this->getResponse(TypeTermsEnum::SALESMAN, null)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                [
                    "term" => [],
                    "userStatus" => null
                ]
            );
    }

    public function test_should_return_an_empty_array_when_term_checked(): void
    {
        $user = factory(User::class)->create();
        $term = $this->createTermWithState(TypeTermsEnum::SALESMAN);
        factory(UserTerm::class)->states('checked')->create([
            'termId' => $term->id,
            'userId' => $user->id,
        ]);

        $this->getResponse(TypeTermsEnum::SALESMAN, $user)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                "term" => [],
                "userStatus" => null
            ]);
    }

    public function test_should_change_status_term_to_checked(): void
    {
        $user     = factory(User::class)->create();
        $term     = $this->createTermWithState(TypeTermsEnum::SALESMAN);
        $userTerm = factory(UserTerm::class)->create([
            'userId' => $user->id,
            'termId' => $term->id,
            'status' => StatusUserTermsEnum::VIEWED
        ]);

        $this->authAs($user)->post(
            self::ROUTE_TERM_ACCEPT,
            [
                'termId' => $term->id
            ]
        )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(
                [
                    'accepted' => true,
                    'userId' => $user->id,
                    'termId' => $term->id
                ]
            );
        $this->assertEquals(StatusUserTermsEnum::CHECKED, $userTerm->refresh()->toArray()['status']);
    }

    public function test_should_accept_false_when_term_not_found(): void
    {
        $this->authAs()->post(
            self::ROUTE_TERM_ACCEPT,
            [
                'termId' => 1
            ]
        )
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'accepted' => false,
            'userId' => null,
            'termId' => null
        ]);
    }

    private function createTermWithState(string $state): Term
    {
        return factory(Term::class)->states($state)->create();
    }

    private function getResponse(string $type, ?User $user): TestResponse
    {
        return $this->authAs($user)->get(self::ROUTE_TERM_USE . $type);
    }
}

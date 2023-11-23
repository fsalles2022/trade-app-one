<?php

declare(strict_types=1);

namespace Terms\Unit;

use Terms\Enums\StatusUserTermsEnum;
use Terms\Enums\TypeTermsEnum;
use Terms\Models\Term;
use Terms\Models\UserTerm;
use Terms\Services\TermService;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class TermServiceTest extends TestCase
{
    use AuthHelper;

    /** @var Term */
    protected $term;

    /** @var UserTerm */
    protected $userTerm;

    /** @var User */
    protected $authenticatedUser;

    private function createNewTerm(string $type = TypeTermsEnum::SALESMAN): void
    {
        $this->term = factory(Term::class)->create([
            'type' => $type
        ]);
    }

    private function createNewUserTerm(string $status = StatusUserTermsEnum::VIEWED): void
    {
        $this->userTerm = factory(UserTerm::class)->create([
            'status' => $status,
            'termId' => ! empty($this->term) ? data_get($this->term, 'id', 0) : 10,
            'userId' => ! empty($this->authenticatedUser) ? data_get($this->authenticatedUser, 'id', 0) : 11
        ]);
    }

    /** @test  */
    public function test_should_be_return_empty_term_when_type_attribute_is_empty(): void
    {
        $this->createNewTerm();

        /** @var TermService $termService */
        $termService = resolve(TermService::class);
        $adaptedTerm = $termService->findTermService([]);

        $this->assertEmpty(data_get($adaptedTerm, 'term'));
        $this->assertNull(data_get($adaptedTerm, 'userStatus'));
    }

    /** @test  */
    public function test_should_be_return_an_term_when_type_attribute_is_valid(): void
    {
        $this->createNewTerm();
        $this->loginWithAuthFacade();

        /** @var TermService $termService */
        $termService = resolve(TermService::class);
        $adaptedTerm = $termService->findTermService([
            'type' => TypeTermsEnum::SALESMAN
        ]);

        $this->assertEquals(StatusUserTermsEnum::VIEWED, data_get($adaptedTerm, 'userStatus'));
        $this->assertNotEmpty(data_get($adaptedTerm, 'term'));

        $this->assertArrayHasKey('id', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('title', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('active', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('urlEmbed', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('type', data_get($adaptedTerm, 'term'));
    }

    /** @test  */
    public function test_should_be_return_empty_term_when_user_term_is_checked(): void
    {
        $this->authenticatedUser = $this->loginWithAuthFacade();
        $this->createNewTerm();
        $this->createNewUserTerm(StatusUserTermsEnum::CHECKED);

        /** @var TermService $termService */
        $termService = resolve(TermService::class);
        $adaptedTerm = $termService->findTermService([
            'type' => TypeTermsEnum::SALESMAN
        ]);

        $this->assertEmpty(data_get($adaptedTerm, 'term'));
        $this->assertNull(data_get($adaptedTerm, 'userStatus'));
    }

    /** @test  */
    public function test_should_be_return_an_term_when_user_term_is_viewed(): void
    {
        $this->authenticatedUser = $this->loginWithAuthFacade();
        $this->createNewTerm();
        $this->createNewUserTerm();

        /** @var TermService $termService */
        $termService = resolve(TermService::class);
        $adaptedTerm = $termService->findTermService([
            'type' => TypeTermsEnum::SALESMAN
        ]);

        $this->assertEquals(StatusUserTermsEnum::VIEWED, data_get($adaptedTerm, 'userStatus'));
        $this->assertNotEmpty(data_get($adaptedTerm, 'term'));

        $this->assertArrayHasKey('id', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('title', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('active', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('urlEmbed', data_get($adaptedTerm, 'term'));
        $this->assertArrayHasKey('type', data_get($adaptedTerm, 'term'));
    }

    /** @test */
    public function should_be_return_error_when_try_accept_non_valid_term(): void
    {
        $this->createNewTerm();
        $this->loginWithAuthFacade();

        /** @var TermService $termService */
        $termService       = resolve(TermService::class);
        $adaptedAcceptTerm = $termService->acceptedUserTerm([
            'termId' => $this->term->id ?? 0
        ]);

        $this->assertFalse(data_get($adaptedAcceptTerm, 'accepted'));
        $this->assertNull(data_get($adaptedAcceptTerm, 'userId'));
        $this->assertNull(data_get($adaptedAcceptTerm, 'termId'));
    }

    /** @test */
    public function should_be_return_user_term_accepted_with_valid_term(): void
    {
        $this->authenticatedUser = $this->loginWithAuthFacade();
        $this->createNewTerm();
        $this->createNewUserTerm();

        /** @var TermService $termService */
        $termService       = resolve(TermService::class);
        $adaptedAcceptTerm = $termService->acceptedUserTerm([
            'termId' => $this->term->id ?? 0
        ]);

        $this->assertTrue(data_get($adaptedAcceptTerm, 'accepted'));
        $this->assertInternalType('int', (int) data_get($adaptedAcceptTerm, 'userId'));
        $this->assertInternalType('int', (int) data_get($adaptedAcceptTerm, 'termId'));
    }
}

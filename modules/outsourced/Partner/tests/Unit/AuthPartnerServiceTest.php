<?php


namespace Outsourced\Partner\tests\Unit;

use Authorization\Models\AvailableRedirect;
use Authorization\Models\Integration;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Services\AuthPartnerService;
use Outsourced\Partner\Services\Clients\InovaPartnerAuthentication;
use Outsourced\Partner\Services\Clients\SivPartnerAuthentication;
use Outsourced\Partner\Services\Clients\ViaVarejoPartnerAuthentication;
use Outsourced\Partner\tests\AuthPartnerTestBook;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Operator;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AuthPartnerServiceTest extends TestCase
{

    private $authPartnerService;

    protected function setUp()
    {
        parent::setUp();
        $this->authPartnerService = resolve(AuthPartnerService::class);
    }

    /** @test */
    public function should_return_integration_instance(): void
    {
        $this->makeIntegrationObject([
            'accessKey' => AuthPartnerTestBook::ACCESS_KEY
        ]);
        $integration = $this->authPartnerService->getPartnerByAccessKey(AuthPartnerTestBook::ACCESS_KEY);
        $this->assertInstanceOf(Integration::class, $integration);
    }

    /** @test */
    public function should_return_error_when_invalid_integration_instance(): void
    {
        $this->expectExceptionObject(PartnerExceptions::notFound());
        factory(Integration::class)->create();
        $this->authPartnerService->getPartnerByAccessKey(AuthPartnerTestBook::ACCESS_KEY);
    }

    /** @test */
    public function should_return_true_when_partner_have_credential_url(): void
    {
        $integration = factory(Integration::class)->create();
        $result      = $this->authPartnerService->partnerHaveCredentialUrl($integration);
        $this->assertTrue($result);
    }

    /** @test */
    public function should_return_exception_when_partner_no_have_credential_url(): void
    {
        $this->expectExceptionObject(PartnerExceptions::invalidCredentialUrl());
        $integration = $this->makeIntegrationObject([
            'credentialVerifyUrl' => null
        ]);
        $this->authPartnerService->partnerHaveCredentialUrl($integration);
    }

    /** @test */
    public function should_return_valid_inova_instance_to_client_partner(): void
    {
        $integration           = $this->makeIntegrationObject([
            'client' => NetworkEnum::INOVA
        ]);
        $partnerImplementation = $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
        $this->assertInstanceOf(InovaPartnerAuthentication::class, $partnerImplementation);
    }

    /** @test */
    public function should_return_valid_siv_instance_to_client_partner(): void
    {
        $integration           = $this->makeIntegrationObject([
            'client' => NetworkEnum::SIV
        ]);
        $partnerImplementation = $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
        $this->assertInstanceOf(SivPartnerAuthentication::class, $partnerImplementation);
    }

    /** @test */
    public function should_return_valid_ViaVarejo_instance_to_client_partner(): void
    {
        $integration           = $this->makeIntegrationObject([
            'client' => NetworkEnum::VIA_VAREJO
        ]);
        $partnerImplementation = $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
        $this->assertInstanceOf(ViaVarejoPartnerAuthentication::class, $partnerImplementation);
    }

    /** @test */
    public function should_return_exception_to_invalid_instance_client_partner(): void
    {
        $this->expectExceptionObject(PartnerExceptions::partnerNotImplemented());
        $integration = $this->makeIntegrationObject([
            'client' => NetworkEnum::RIACHUELO
        ]);
        $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
    }

    /** @test */
    public function should_return_true_when_user_belongs_to_partner(): void
    {
        $network     = (new NetworkBuilder())->build();
        $operator    = factory(Operator::class)->create();
        $user        = (new UserBuilder())->withNetwork($network)->withOperators($operator->id)->build();
        $integration = $this->makeIntegrationObject([
            'networkId' => $network->id,
            'operatorId' => $operator->id
        ]);

        $result = $this->authPartnerService->userBelongsToPartner(
            $integration,
            $user
        );
        $this->assertTrue($result);
    }

    /** @test */
    public function should_return_exception_when_user_not_belongs_to_partner(): void
    {
        $this->expectExceptionObject(PartnerExceptions::userNotBelongsPartner());

        $network     = (new NetworkBuilder())->build();
        $user        = (new UserBuilder())->withNetwork($network)->build();
        $integration = $this->makeIntegrationObject([
            'networkId' => $network->id
        ]);
        $this->authPartnerService->userBelongsToPartner(
            $integration,
            $user
        );
    }

    /** @test */
    public function should_return_jwt_token_when_user_is_valid(): void
    {
        $user = (new UserBuilder())->build();
        $jwt  = $this->authPartnerService->getJwtTokenFromUser($user);
        $this->assertNotNull($jwt);
    }

    /** @test */
    public function should_return_user_unauthorized_when_user_is_invalid(): void
    {
        $this->expectException(UserNotFoundException::class);

        $user      = (new UserBuilder())->build();
        $user->cpf = '000';
        $this->authPartnerService->getJwtTokenFromUser($user);
    }

    /** @test */
    public function should_return_md5_key_from_integration(): void
    {
        $user        = (new UserBuilder())->build();
        $integration = $this->makeIntegrationObject([
            'client' => NetworkEnum::SIV,
            'networkId' => null,
            'operatorId' => null
        ]);
        factory(AvailableRedirect::class)->create([
            'integrationId' => $integration->id,
            'defaultUrl' => true
        ]);
        $partnerImplementation = $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
        $token                 = $this->authPartnerService->getJwtTokenFromUser($user);

        $md5Key = $this->authPartnerService->cacheUrl($token, $integration, $partnerImplementation, null);
        $this->assertNotNull($md5Key);
    }

    /** @test */
    public function should_return_md5_key_error_when_integration_no_have_default_redirect_url(): void
    {
        $this->expectExceptionObject(PartnerExceptions::notFoundDefaultRedirectUrl());

        $user                  = (new UserBuilder())->build();
        $integration           = $this->makeIntegrationObject([
            'client' => NetworkEnum::SIV,
            'networkId' => null,
            'operatorId' => null
        ]);
        $partnerImplementation = $this->authPartnerService->getPartnerClient(
            $integration,
            AuthPartnerTestBook::VALID_TOKEN
        );
        $token                 = $this->authPartnerService->getJwtTokenFromUser($user);
        $this->authPartnerService->cacheUrl($token, $integration, $partnerImplementation, null);
    }

    protected function makeIntegrationObject(array $attributes = []): Integration
    {
        return factory(Integration::class)->create($attributes);
    }
}

<?php


namespace Outsourced\Partner\tests\Feature;

use Authorization\Models\AvailableRedirect;
use Authorization\Models\Integration;
use Outsourced\Partner\tests\AuthPartnerTestBook;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AuthPartnerFeatureTest extends TestCase
{
    use AuthHelper;

    private const PARTNER_AUTH_URI = '/partner/authentication';

    protected function setUp()
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    /** @test */
    public function should_return_url_authenticated_when_valid_integration(): void
    {
        (new UserBuilder())
            ->withCustomParameters(['cpf' => AuthPartnerTestBook::VALID_CPF])
            ->build();

        $response = $this->authAs()->post(self::PARTNER_AUTH_URI, [
            'accessKey' => AuthPartnerTestBook::ACCESS_KEY,
            'token' => AuthPartnerTestBook::VALID_TOKEN
        ]);
        $data     = $response->json();
        $response->assertJsonStructure(['url']);
        $response->assertStatus(\Illuminate\Http\Response::HTTP_OK);
        $this->assertNotNull(data_get($data, 'url'));
    }

    private function setUpDatabase(): Integration
    {
        $integration = factory(Integration::class)->create([
            'client' => NetworkEnum::SIV,
            'subdomain' => NetworkEnum::SIV,
            'networkId' => null,
            'operatorId' => null,
            'accessKey' => AuthPartnerTestBook::ACCESS_KEY,
            'credentialVerifyUrl' => AuthPartnerTestBook::CREDENTIAL_VERIFY_URL_PATH .
                AuthPartnerTestBook::CREDENTIAL_VERIFY_URL_URI
        ]);
        factory(AvailableRedirect::class)->create([
            'integrationId' => $integration->id,
            'defaultUrl' => true
        ]);
        return $integration;
    }
}

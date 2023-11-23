<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroDomainsTest extends TestCase
{
    const ENDPOINT = '/clarobr/domains';

    use AuthHelper, SivBindingHelper;

    /** @test */
    public function should_response_with_200_when_called()
    {
        $userHelper = (new UserBuilder())->build();
        $this->bindSivResponse();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->withHeader('client', SubSystemEnum::WEB)
            ->json('GET', '/' . self::ENDPOINT);

        $response->assertStatus(Response::HTTP_OK);
    }
}

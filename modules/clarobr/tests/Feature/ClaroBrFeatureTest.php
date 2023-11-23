<?php

namespace ClaroBR\Tests\Feature;

use ClaroBR\Tests\ServerTest\SivBindingHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\TestCase;

class ClaroBrFeatureTest extends TestCase
{
    use SivBindingHelper, AuthHelper;

    /** @test */
    public function should_return_correct_userLists()
    {
        $this->bindSivResponse();

        $response = $this->authAs()->post('/sales/siv/user-lines', [
            'cpf' => '00000009652'
        ]);

        $response->assertJsonStructure([
            0 => [
                'phone',
                'type',
                'status'
            ]
        ]);
    }
}

<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Domain\Rules\StatesBRValidationRule;
use TradeAppOne\Tests\TestCase;

class StatesBRValidatorTest extends TestCase
{
    protected $statesBRValidationRule;

    protected function setUp()
    {
        $this->statesBRValidationRule = new StatesBRValidationRule;
    }

    /** @test */
    public function should_return_boolean_when_passes_function_is_called()
    {
        $returnedValue = $this->statesBRValidationRule->passes('state', '');

        $this->assertTrue(is_bool($returnedValue));
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_AC()
    {
        $state = 'AC';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_AL()
    {
        $state = 'AL';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function shhould_return_true_when_state_abbreviation_is_AM()
    {
        $state = 'AM';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_AP()
    {
        $state = 'AP';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_BA()
    {
        $state = 'BA';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_CE()
    {
        $state = 'CE';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_DF()
    {
        $state = 'DF';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_ES()
    {
        $state = 'ES';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_GO()
    {
        $state = 'GO';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_are_code_prefix_is_MA()
    {
        $state = 'MA';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_MG()
    {
        $state = 'MG';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_MS()
    {
        $state = 'MS';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_MT()
    {
        $state = 'MT';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_PA()
    {
        $state = 'PA';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_PB()
    {
        $state = 'PB';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_PE()
    {
        $state = 'PE';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_PI()
    {
        $state = 'PI';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_PR()
    {
        $state = 'PR';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_RJ()
    {
        $state = 'RJ';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_RN()
    {
        $state = 'RN';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_RO()
    {
        $state = 'RO';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_RR()
    {
        $state = 'RR';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_RS()
    {
        $state = 'RS';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_SC()
    {
        $state = 'SC';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_SE()
    {
        $state = 'SE';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_SP()
    {
        $state = 'SP';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_state_abbreviation_is_TO()
    {
        $state = 'TO';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_when_state_abbreviation_is_TE()
    {
        $state = 'TE';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_state_abbreviation_is_AS()
    {
        $state = 'AS';

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_state_abbreviation_is_too_long()
    {
        $state = str_random(257);

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertFalse($isValid);
    }


    /** @test */
    public function should_return_false_when_state_abbreviation_is_null()
    {
        $state = null;

        $isValid = $this->statesBRValidationRule->passes('state', $state);

        $this->assertFalse($isValid);
    }
}

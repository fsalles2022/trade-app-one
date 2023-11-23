<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Domain\Rules\AreaCodePrefixValidationRule;
use TradeAppOne\Tests\TestCase;

class AreaCodePrefixValidatorTest extends TestCase
{

    protected $areaCodePrefixValidationRule;

    protected function setUp()
    {
        $this->areaCodePrefixValidationRule = new AreaCodePrefixValidationRule;
    }

    /** @test */
    public function should_return_boolean_when_passes_function_is_called()
    {
        $returnedValue = $this->areaCodePrefixValidationRule->passes('area_code', '');

        $this->assertTrue(is_bool($returnedValue));
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_11()
    {
        $code = '11';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_12()
    {
        $code = '12';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_13()
    {
        $code = '13';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_14()
    {
        $code = '14';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_15()
    {
        $code = '15';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_16()
    {
        $code = '16';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_17()
    {
        $code = '17';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_18()
    {
        $code = '18';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_19()
    {
        $code = '19';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_are_code_prefix_is_21()
    {
        $code = '21';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_22()
    {
        $code = '22';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_24()
    {
        $code = '24';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_27()
    {
        $code = '27';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_28()
    {
        $code = '28';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_31()
    {
        $code = '31';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_32()
    {
        $code = '32';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_33()
    {
        $code = '33';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_34()
    {
        $code = '34';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_35()
    {
        $code = '35';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_36()
    {
        $code = '37';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_38()
    {
        $code = '38';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_41()
    {
        $code = '41';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_42()
    {
        $code = '42';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_43()
    {
        $code = '43';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_44()
    {
        $code = '44';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_45()
    {
        $code = '45';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_46()
    {
        $code = '46';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_47()
    {
        $code = '47';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_48()
    {
        $code = '48';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_49()
    {
        $code = '49';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_51()
    {
        $code = '51';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_53()
    {
        $code = '53';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_54()
    {
        $code = '54';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_55()
    {
        $code = '55';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_61()
    {
        $code = '61';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_62()
    {
        $code = '62';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_63()
    {
        $code = '63';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_64()
    {
        $code = '64';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_65()
    {
        $code = '65';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_66()
    {
        $code = '66';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_67()
    {
        $code = '67';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_68()
    {
        $code = '68';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_69()
    {
        $code = '69';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_71()
    {
        $code = '71';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_73()
    {
        $code = '73';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_74()
    {
        $code = '74';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_75()
    {
        $code = '75';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_77()
    {
        $code = '77';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_79()
    {
        $code = '79';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_81()
    {
        $code = '81';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_82()
    {
        $code = '82';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_83()
    {
        $code = '83';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_84()
    {
        $code = '84';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_85()
    {
        $code = '85';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_86()
    {
        $code = '86';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_87()
    {
        $code = '87';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_89()
    {
        $code = '89';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_91()
    {
        $code = '91';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_92()
    {
        $code = '92';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_93()
    {
        $code = '93';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_94()
    {
        $code = '94';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_95()
    {
        $code = '95';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_96()
    {
        $code = '96';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_97()
    {
        $code = '97';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_98()
    {
        $code = '98';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_when_area_code_prefix_is_99()
    {
        $code = '99';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_00()
    {
        $code = '00';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_01()
    {
        $code = '01';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_02()
    {
        $code = '02';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_03()
    {
        $code = '03';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_04()
    {
        $code = '04';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_05()
    {
        $code = '05';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_06()
    {
        $code = '06';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_07()
    {
        $code = '07';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_08()
    {
        $code = '08';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_09()
    {
        $code = '09';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_10()
    {
        $code = '10';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_20()
    {
        $code = '20';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_23()
    {
        $code = '23';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_25()
    {
        $code = '25';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_26()
    {
        $code = '26';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_29()
    {
        $code = '29';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_30()
    {
        $code = '30';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_36()
    {
        $code = '36';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_39()
    {
        $code = '39';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_40()
    {
        $code = '40';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_50()
    {
        $code = '50';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_52()
    {
        $code = '52';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_retunr_false_when_area_code_prefix_is_56()
    {
        $code = '56';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_57()
    {
        $code = '57';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_58()
    {
        $code = '58';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_59()
    {
        $code = '59';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_60()
    {
        $code = '60';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_70()
    {
        $code = '70';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_72()
    {
        $code = '72';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_76()
    {
        $code = '76';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_78()
    {
        $code = '78';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_80()
    {
        $code = '80';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_90()
    {
        $code = '90';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_100()
    {
        $code = '100';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_empty()
    {
        $code = '';

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_area_code_prefix_is_null()
    {
        $code = null;

        $isValid = $this->areaCodePrefixValidationRule->passes('area_code', $code);

        $this->assertFalse($isValid);
    }
}

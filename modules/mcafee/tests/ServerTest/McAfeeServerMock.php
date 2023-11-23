<?php

namespace McAfee\Tests\ServerTest;

class McAfeeServerMock extends \StdClass
{
    public $ProcessRequestWSResult;

    public function __construct(int $code = 1000)
    {
        $this->ProcessRequestWSResult =
            "<PARTNERRESPONSECONTEXT>
                <HEADER>
                    <PARTNER PARTNER_ID=\"737\" />
                </HEADER>
                <DATA>
                    <RESPONSECONTEXT ID=\"ABC123d45\"> <RETURNCODE>$code</RETURNCODE> <RETURNDESC>Transaction is successful</RETURNDESC>
                    <ORDER PARTNERREF=\"XYZ12d345\" REF=\"NCS1230361\">
                    <ITEMS>
                        <ITEM SKU=\"737-53571-mmsu\" EXPDT=\"2014-07-12\">
                        <PRODUCTKEY>r0EHYt5McOPElSK2hUEsvr2yB3S/xbHcR7XXBPqtKJXZqDP/3p9KV29wnmQqs+wi</PRODUCTKEY> 
                        <PHONE ACTIVATIONCODE=\"RGYABC\" />
                        </ITEM>
                    </ITEMS>
                    </ORDER>
                    </RESPONSECONTEXT>
                </DATA>
            </PARTNERRESPONSECONTEXT>";
    }
}

<?php

declare(strict_types=1);

namespace OiBR\Tests\Unit\Services;

use OiBR\Assistance\OiBRService;
use TradeAppOne\Tests\TestCase;

class OiBRServiceTest extends TestCase
{
    /** @test */
    public function should_return_array_with_links(): void
    {
        $oiBrService = resolve(OiBRService::class);
        
        $oiSaleFlowLink        = 'https://oilogin360.oi.net.br/';
        $documentCertification = 'https://ged360.oi.net.br/brprontopdv/';
            
        config(['integrations.oiBR.oiSaleFlow' => $oiSaleFlowLink]);
        config(['integrations.oiBR.documentCertification' => $documentCertification]);
            
        $residentialLinks = $oiBrService->getResidentialLinks();

        $this->assertContains($oiSaleFlowLink, $residentialLinks);
        $this->assertContains($documentCertification, $residentialLinks);
    }
}

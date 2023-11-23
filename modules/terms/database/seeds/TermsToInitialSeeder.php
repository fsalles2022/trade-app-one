<?php

declare(strict_types=1);

namespace Terms\database\seeds;

use Illuminate\Database\Seeder;
use Terms\Enums\TypeTermsEnum;
use Terms\Models\Term;

/**
 * Class TermsToInitialSeeder
 * Description: Initial Terms for UseTerms in Trade App One - Populate by database.
 *
 */
class TermsToInitialSeeder extends Seeder
{
    public function run(): void
    {
        $salesmanTerm           = new Term();
        $salesmanTerm->title    = 'Termo de Uso - Vendedor';
        $salesmanTerm->active   = 1;
        $salesmanTerm->type     = TypeTermsEnum::SALESMAN;
        $salesmanTerm->urlEmbed = 'https://tradeapp-one-test.s3.amazonaws.com/public/terms/use/termo-privacidade-tradeappone-v1.pdf';

        $salesmanTerm->save();


        $customerTerm           = new Term();
        $customerTerm->title    = 'Termo de Uso - Cliente';
        $customerTerm->active   = 1;
        $customerTerm->type     = TypeTermsEnum::CUSTOMER;
        $customerTerm->urlEmbed = 'https://tradeapp-one-test.s3.amazonaws.com/public/terms/use/termo-privacidade-tradeappone-v1.pdf';

        $customerTerm->save();
    }
}

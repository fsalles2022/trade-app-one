<?php

namespace TimBR\Models;

use Illuminate\Support\Collection;

/**
 * @property Collection products
 * @property string eligibilityToken
 */
class Eligibility
{
    public $products;
    public $eligibilityToken;
}

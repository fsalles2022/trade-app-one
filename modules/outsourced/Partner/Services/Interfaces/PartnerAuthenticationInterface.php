<?php


namespace Outsourced\Partner\Services\Interfaces;

use Authorization\Models\AvailableRedirect;
use TradeAppOne\Domain\Models\Tables\User;

interface PartnerAuthenticationInterface
{
    public function retrieveUserIdentificationDocument(): void;
    public function getSignInUrl($md5Key, $subdomain = null): string;
    public function getAvailableRedirectUrl(): ?AvailableRedirect;
    public function getUserFromDocument(): ?User;
}

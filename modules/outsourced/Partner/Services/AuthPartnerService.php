<?php

namespace Outsourced\Partner\Services;

use Authorization\Models\AvailableRedirect;
use Authorization\Models\Integration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use Outsourced\Partner\Helpers\Builders\PartnerAuthenticationBuilder;
use Outsourced\Partner\Services\Interfaces\PartnerAuthenticationInterface;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Domain\Services\HierarchyService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class AuthPartnerService
{
    protected $userService;
    protected $hierarchyService;
    protected $authService;

    public function __construct(UserService $userService, HierarchyService $hierarchyService, AuthService $authService)
    {
        $this->userService        = $userService;
        $this->hierarchyService   = $hierarchyService;
        $this->authService        = $authService;
    }

    public function getAuthenticatedUrl(array $params): string
    {
        $accessKey = data_get($params, 'accessKey');
        $token     = data_get($params, 'token');
        $route     = data_get($params, 'route');

        $integration = $this->getPartnerByAccessKey($accessKey);
        $this->partnerHaveCredentialUrl($integration);
        $partner = $this->getPartnerClient($integration, $token);

        if ($partner instanceof PartnerAuthenticationInterface) {
            $partner->retrieveUserIdentificationDocument();
        }
        $user = $partner->getUserFromDocument();
        $this->userBelongsToPartner($integration, $user);
        $authToken   = $this->getJwtTokenFromUser($user);
        $authKey     = $this->cacheUrl($authToken, $integration, $partner, $route);

        return $partner->getSignInUrl($authKey, $integration->subdomain);
    }

    public function getCredentialsFromMD5(string $md5) : array
    {
        if ($token = Cache::get($md5)) {
            return [
                'access_token' => $token['bearer'],
                'redirect_url' => $token['redirect_url']->redirectUrl
            ];
        }
        throw PartnerExceptions::tokenInvalidOrExpired();
    }

    public function getPartnerByAccessKey(string $accessKey): Integration
    {
        try {
            return Integration::where('accessKey', $accessKey)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw PartnerExceptions::notFound();
        }
    }

    public function userBelongsToPartner(Integration $partner, User $user = null): bool
    {
        if ($partner->networkId !== null) {
            $belongsToNetwork = $this->checkUserBelongsNetwork($partner->networkId, $user);
            if ($belongsToNetwork === false) {
                throw PartnerExceptions::userNotBelongsPartner();
            }
        }

        if ($partner->operatorId !== null) {
            $belongsToOperator = $this->checkUserBelongsOperator($partner->operatorId, $user);
            if ($belongsToOperator === false) {
                throw PartnerExceptions::userNotBelongsPartner();
            }
        }

        return true;
    }

    private function checkUserBelongsNetwork(int $networkId, ?User $user): bool
    {
        $userNetworks = $this->hierarchyService->getNetworksThatBelongsToUser($user);
        $filter       = $userNetworks->filter(function ($network) use ($networkId) {
            return $networkId === $network->id;
        });
        return ! ($filter->count() === 0);
    }

    private function checkUserBelongsOperator(int $operatorId, ?User $user): bool
    {
        if ($user) {
            $userOperators = $user->operators()->get();
            $filter        = $userOperators->filter(static function ($operator) use ($operatorId) {
                return $operatorId === $operator->id;
            });
            return ! ($filter->count() === 0);
        }
        return false;
    }

    public function getJwtTokenFromUser(User $user): string
    {
        $tokenWithUser = $this->authService->loginUser($user->cpf);
        $jwt           = data_get($tokenWithUser, '1');
        if ($jwt !== null) {
            return $jwt;
        }
        throw UserExceptions::userUnauthorized();
    }

    public function cacheUrl(string $jwt, Integration $integration, PartnerAuthenticationInterface $partner, ?string $route): string
    {
        $key = md5($jwt);

        $redirectUrl = $this->getDefaultPartnerRoute($integration);

        if ($route !== null) {
            $redirectUrl = AvailableRedirect::findByRouteKey($route) ?? $redirectUrl;
        }

        $partnerRedirectUrl = $partner->getAvailableRedirectUrl();
        if ($partnerRedirectUrl) {
            $redirectUrl = $partnerRedirectUrl;
        }

        $credential = [
            'bearer'       => $jwt,
            'redirect_url' => $redirectUrl,
        ];

        Cache::put($key, $credential, 10);
        return $key;
    }

    public function getPartnerClient(Integration $partner, string $token): ?PartnerAuthenticationInterface
    {
        try {
            return PartnerAuthenticationBuilder::create()
                ->forPartner($partner)
                ->andToken($token)
                ->build();
        } catch (BuildExceptions $e) {
            throw PartnerExceptions::partnerNotImplemented($e->getMessage());
        }
    }

    public function partnerHaveCredentialUrl(Integration $partner): bool
    {
        if ($partner->credentialVerifyUrl === null) {
            throw PartnerExceptions::invalidCredentialUrl();
        }
        return true;
    }

    public function getDefaultPartnerRoute(Integration $partner): AvailableRedirect
    {
        try {
            return AvailableRedirect::where('integrationId', $partner->id)
                ->where('defaultUrl', true)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw PartnerExceptions::notFoundDefaultRedirectUrl();
        }
    }
}

<?php


namespace ClaroBR\Connection;

use ClaroBR\Enumerators\ClaroBRCaches;
use ClaroBR\Exceptions\SivAuthExceptions;
use Illuminate\Support\Facades\Cache;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\AuthService;

class SivAuth
{
    protected $sivConnection;
    protected $authService;

    public function __construct(SivConnection $sivConnection, AuthService $authService)
    {
        $this->sivConnection = $sivConnection;
        $this->authService   = $authService;
    }

    public function promoter(array $promoterForm)
    {
        $cpf  = data_get($promoterForm, 'cpf');
        $user = User::query()->where('cpf', $cpf)->first();

        if ($cpf !== null) {
            throw_if($user === null, SivAuthExceptions::sivUserNotFound());
        }
        $response = $this->auth($promoterForm);
        $custCode = data_get($response, 'pos');

        if ($custCode) {
            return $this->syncUserAndPointOfSale($custCode, $user);
        }

        if ($providerIdentifier = data_get($promoterForm, 'codigo_pdv')) {
            return $this->syncUserAndPointOfSale($providerIdentifier, $user);
        }

        return $response;
    }

    public function auth(array $attributes): array
    {
        $cpf      = data_get($attributes, 'cpf');
        $userName = data_get($attributes, 'username');

        $firstAccess = Cache::get(ClaroBRCaches::SIV_PROMOTER_FIRST_AUTH . $userName);

        if ($firstAccess && $cpf) {
            $response = $this->sivConnection->promoterAuth($attributes);
        } else {
            unset($attributes['cpf']);
            $response = $this->sivConnection->promoterAuth($attributes);
        }

        if ($cpf !== data_get($response, 'cpf')) {
            throw SivAuthExceptions::sivUserAlreadyLogged();
        }

        $tokenBearer = data_get($response, 'token');
        Cache::put(ClaroBRCaches::USER_BEARER . $cpf, $tokenBearer, ClaroBRCaches::AUTHENTICATION_DUE);

        $promotorId = data_get($response, 'promotor_id');
        Cache::put(ClaroBRCaches::PROMOTOR_ID . $cpf, $promotorId, ClaroBRCaches::AUTHENTICATION_DUE);
        Cache::forget(ClaroBRCaches::SIV_PROMOTER_FIRST_AUTH . $userName);

        return $response;
    }

    private function syncUserAndPointOfSale(string $code, $user)
    {
        $pointOfSale = $this->findPointOfSale('providerIdentifiers', $code);
        $user->pointsOfSale()->sync($pointOfSale);

        [$user, $token]      = $this->authService->loginUser($user->cpf);
        $response['message'] = trans('messages.token_created');
        $response['data']    = ['token' => $token, 'user' => $user];

        return $response;
    }

    protected function findPointOfSale(string $key, string $value)
    {
        $pointOfSale = PointOfSale::query()->where($key, 'like', "%$value%")->first();
        if ($pointOfSale) {
            return $pointOfSale;
        }

        throw SivAuthExceptions::sivPointOfSaleNotFound();
    }
}

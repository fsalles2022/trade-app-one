<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\Traits\ArrayHelper;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends BaseService
{
    use ArrayHelper;

    /** @var Agent */
    private $agentService;

    /** @var UserService */
    private $userService;

    public function __construct(UserService $userService, Agent $agentService)
    {
        $this->agentService = $agentService;
        $this->userService  = $userService;
    }

    public function existsRestrictionToSignin(string $cpf): ?array
    {
        $user = $this->userService->findBy($cpf);

        if ($user) {
            if ($userAttempts = $this->checkUserAttempts($user)) {
                return $userAttempts;
            }

            if ($firstAccessRestriction = $this->checkUserFirstAccessRestriction($user)) {
                return $firstAccessRestriction;
            }

            if ($userPermissions = $this->checkUserPermissions($user)) {
                return $userPermissions;
            }
            if ($statusRestriction = $this->checkUserStatus($user)) {
                return $statusRestriction;
            }
        }

        return null;
    }

    private function checkUserAttempts(User $user)
    {
        if ($user->signinAttempts > User::ATTEMPTS_LIMIT) {
            $response = ['message' => trans('exceptions.sign_in.exceeded_sign_in_attempts'),];
            return ['response' => $response, 'status' => Response::HTTP_UNAUTHORIZED];
        }

        return null;
    }

    private function checkUserFirstAccessRestriction(User $user): ?array
    {
        $isUserNonVerified = ($user->activationStatusCode === UserStatus::NON_VERIFIED);

        if ($isUserNonVerified) {
            $checkIfUserHasVerificationCode = DB::table('userVerifications')
                ->where('userId', data_get($user, 'id'))
                ->first();

            if ($checkIfUserHasVerificationCode === null) {
                $verificationCode = $this->userService->generateAndRegisterVerificationCode($user);
            } else {
                $verificationCode = data_get($checkIfUserHasVerificationCode, 'verificationCode');
            }


            $response = [
                'message' => trans('messages.user.first_access'),
                'data' => ['verificationCode' => $verificationCode]
            ];
            return ['response' => $response, 'status' => Response::HTTP_PRECONDITION_REQUIRED];
        }

        return null;
    }

    private function checkUserPermissions(User $user)
    {
        $listAllPermissions = $user->role->stringPermissions()->get();
        if ($listAllPermissions->isEmpty() || $listAllPermissions->get(SubSystemEnum::WEB)) {
            $content = ['error' => trans('messages.not_authorized')];
            return ['response' => $content, 'status' => Response::HTTP_UNAUTHORIZED];
        }
    }

    private function checkUserStatus(User $user)
    {
        if (in_array(
            $user->activationStatusCode,
            [UserStatus::NON_VERIFIED, UserStatus::INACTIVE, UserStatus::VERIFIED]
        )) {
            $response = ['message' => trans('exceptions.sign_in.status.' . $user->activationStatusCode),];
            return ['response' => $response, 'status' => Response::HTTP_UNAUTHORIZED];
        }

        return null;
    }

    public function loginUser(string $cpf): array
    {
        $user  = $this->userService->findOneByCpf($cpf);
        $token = JWTAuth::fromUser($user);
        Auth::login($user);
        $user = $this->loadAvailableServices($user);
        $this->userService->makeActiveToken($token);
        return [$user, $token];
    }

    public function loadAvailableServices($user)
    {
        $this->activeEagerLoadToAccessPointsOfSale($user);
        $user['role']['permissions'] = $this->filterPermissions($user);
        $user['availableServices']   = $this->getAvailableServices($user);
        $user['operators']           = $user->getOperators();

        return $user;
    }

    public function getAvailableServices(User $user)
    {
        $pointOfSale = $user->pointsOfSale->first();
        $network     = $user->getNetwork();
        $operator    = $user->operators()->get();
        $services    = [];

        if ($operator->count() > 0) {
            return $this->getPromotorAvailableServices($operator, $pointOfSale, $network);
        }

        if (is_object($pointOfSale) && $pointOfSale->services()->count()) {
            $services = $this->array_merge_recursive_distinct($services, $pointOfSale->availableServicesRelation);
        }

        if (is_object($network) && $network->services()->count()) {
            $services = $this->array_merge_recursive_distinct($services, $network->availableServicesRelation);
        }

        return $services;
    }

    private function getPromotorAvailableServices(Collection $operator, PointOfSale $pointOfSale, Network $network)
    {
        $availablesArray = $operator->map(static function ($op) {
            return $op->availableServices;
        })->toArray();

        $permissionsAvailable = array_merge_recursive(...$availablesArray);

        if ($pointOfSale && $pointOfSale->services()->count()) {
            return $this->array_intersect_recursive($permissionsAvailable, $pointOfSale->availableServicesRelation);
        }

        if (is_object($network) && $network->services()->count()) {
            return $this->array_intersect_recursive($permissionsAvailable, $network->availableServicesRelation);
        }

        return [];
    }

    private function activeEagerLoadToAccessPointsOfSale(&$user)
    {
        foreach ($user->pointsOfSale as $point) {
            $point->network;
        }
        return $user;
    }

    private function filterPermissions($user)
    {
        return $user->role->permissions;
    }

    public function getAuthenticatedUser(): ?User
    {
        $user = Auth::user();
        $user = $this->loadAvailableServices($user);
        return $user;
    }

    public function isCredentialsInvalid(array $credentials): bool
    {
        return ! Auth::validate($credentials);
    }

    public function logFailAttempt(string $cpf): void
    {
        $this->userService->logFailAttempt($cpf);
    }

    public function logSucessAttempt($user): void
    {
        $this->userService->logSuccessAttempt($user);
    }

    public function logAccess(Request $request, int $userId, bool $isLogOn = true): void
    {
        $this
            ->userService
            ->createAccessLog(
                $this->makeLogAccess($request, $isLogOn),
                $userId
            );
    }

    private function makeLogAccess(Request $request, bool $isLogOn): array
    {

        $agent = $this->agentService->browser() . " in " . $this->agentService->platform();
        $robot = $this->agentService->isRobot() ? $this->agentService->robot() : null;

        if ($agent == ' in ') {
            $agent = request()->header('User-Agent');
        }

        return [
            'ip' => $request->getClientIp(),
            'device' => $robot ? $robot : $agent,
            'type' => $isLogOn ? 'signin' : 'signout',
            'requestedUrl' => $request->getRequestUri(),
        ];
    }

    public function getAlternativeAuth(string $document): ?User
    {
        return $this->userService->findOneByAlternate($document);
    }
}

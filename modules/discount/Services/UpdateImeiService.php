<?php

declare(strict_types=1);

namespace Discount\Services;

use Discount\Enumerators\ImeiEnum;
use Discount\Events\ImeiUpdateEvent;
use Discount\Exceptions\ImeiExceptions;
use Discount\Services\Input\AuthorizationUpdateImeiInput;
use Discount\Services\Input\GetSaleWithImeiInput;
use Discount\Services\Input\UpdateImeiServiceInput;
use Discount\Services\Output\GetAuthorizationImeiOutput;
use Discount\Services\Output\GetSaleWithimeiList0utput;
use Discount\Services\Output\GetSaleWithImeiOutput;
use Discount\Services\Output\Output;
use Discount\Services\Output\UpdateImeiServiceOutput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Domain\Services\UserService;

class UpdateImeiService
{
    /** @var UserService */
    private $userService;

    /** @var SaleService */
    private $saleService;

    public function __construct(UserService $userService, SaleService $saleService)
    {
        $this->userService = $userService;
        $this->saleService = $saleService;
    }

    public function getInformationAboutSale(GetSaleWithImeiInput $getSaleWithImeiInput): Output
    {
        if ($getSaleWithImeiInput->getServiceTransaction() !== null) {
            $service = $this->saleService->findService($getSaleWithImeiInput->getServiceTransaction());
            return $this->hydrateOutput($service, null);
        }

        if ($getSaleWithImeiInput->getCpf() !== null) {
            $sales  = $this->saleService->getSalesByCustomerCpf($getSaleWithImeiInput->getCpf());
            $output = new GetSaleWithimeiList0utput();

            $sales->each(function (Sale $sale) use (&$output) {
                foreach ($sale->services as $service) {
                    $output->addGetSaleWithImeiOutput($this->hydrateOutput($service, $sale));
                }
            });

            return $output;
        }

        return $this->hydrateOutput(null, null);
    }

    private function hydrateOutput(?Service $service, ?Sale $sale): GetSaleWithImeiOutput
    {
        return new GetSaleWithImeiOutput(
            $service->serviceTransaction ?? null,
            $service->customer['cpf'] ?? null,
            $service->imei ?? null,
            $service->customer['firstName'] ?? null,
            $service->customer['lastName'] ?? null,
            is_null($sale) ? null : $sale->createdAt->format('Y-m-d')
        );
    }

    public function authorize(AuthorizationUpdateImeiInput $authorizationUpdateImeiInput): Output
    {
        $user = $this->userService->getUserByCpf($authorizationUpdateImeiInput->getLogin());

        throw_unless(
            $this->isAuthorized($authorizationUpdateImeiInput, $user),
            ImeiExceptions::unauthorized()
        );

        return new GetAuthorizationImeiOutput($this->generatedHash($authorizationUpdateImeiInput, $user));
    }

    private function isAuthorized(AuthorizationUpdateImeiInput $authorizationUpdateImeiInput, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasPermission(ImeiEnum::PERMISSION . '.' . PermissionActions::EDIT) === false) {
            return false;
        }

        return Hash::check($authorizationUpdateImeiInput->getPassword(), $user->getPassword());
    }

    private function generatedHash(AuthorizationUpdateImeiInput $authorizationUpdateImeiInput, ?User $user): string
    {
        Cache::forget($user->cpf . $authorizationUpdateImeiInput->getServiceTransaction() . ImeiEnum::CACHE_HASH_IMEI);
        $hash = password_hash($user->cpf . time(), PASSWORD_BCRYPT);
        Cache::put(
            $user->cpf . $authorizationUpdateImeiInput->getServiceTransaction() . ImeiEnum::CACHE_HASH_IMEI,
            $hash,
            ImeiEnum::EXPIRE_CACHE_IMEI
        );

        return $hash;
    }

    public function updateImeiInService(UpdateImeiServiceInput $updateImeiServiceInput): Output
    {
        $this->checkAuthorizationHash($updateImeiServiceInput);

        $service = $this->saleService->findService($updateImeiServiceInput->getServiceTransaction());

        $serviceUpdated = $this->saleService->updateImei($service, $updateImeiServiceInput->getNewImei());

        event(new ImeiUpdateEvent($serviceUpdated, $updateImeiServiceInput));

        return new UpdateImeiServiceOutput($serviceUpdated->getImei() === $updateImeiServiceInput->getNewImei());
    }

    private function checkAuthorizationHash(UpdateImeiServiceInput $updateImeiServiceInput): void
    {
        $hash = Cache::get(
            $updateImeiServiceInput->getAuthorizerCpf() .
            $updateImeiServiceInput->getServiceTransaction() .
            ImeiEnum::CACHE_HASH_IMEI
        );

        throw_unless($hash === $updateImeiServiceInput->getAuthorization(), ImeiExceptions::unauthorized());
    }
}

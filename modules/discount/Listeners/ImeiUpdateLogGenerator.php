<?php

declare(strict_types=1);

namespace Discount\Listeners;

use Discount\Events\ImeiUpdateEvent;
use Discount\Repositories\DTOs\ImeiChangeHistoryCreateDto;
use Discount\Repositories\ImeiChangeHistoryRepository;
use Discount\Services\Input\UpdateImeiServiceInput;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\UserService;

class ImeiUpdateLogGenerator
{
    /** @var ImeiChangeHistoryRepository */
    private $imeiChangeHistoryRepository;

    /** @var UserService */
    private $userService;

    public function __construct(
        ImeiChangeHistoryRepository $imeiChangeHistoryRepository,
        UserService $userService
    ) {
        $this->imeiChangeHistoryRepository = $imeiChangeHistoryRepository;
        $this->userService                 = $userService;
    }

    public function handle(ImeiUpdateEvent $imeiUpdateEvent): void
    {
        /** @var UpdateImeiServiceInput $input */
        $input = $imeiUpdateEvent->getInput();

        $userWhoAuthorized = $this->getUser($input->getAuthorizerCpf());

        $this->imeiChangeHistoryRepository->save(
            new ImeiChangeHistoryCreateDto(
                $imeiUpdateEvent->getService()['serviceTransaction'] ?? null,
                $input->getOldImei(),
                $imeiUpdateEvent->getService()['imei'] ?? null,
                $this->isSetId(Auth::user()->id),
                Auth::user()->cpf,
                $this->isSetId($userWhoAuthorized->id ?? null),
                $userWhoAuthorized->cpf ?? null,
                now()->format('Y-m-d H:i:s'),
                $input->getAuthorization()
            )
        );
    }

    private function getUser(?string $cpf): ?User
    {
        return $this->userService->getUserByCpf($cpf);
    }

    /** @param string|int|null $userId */
    private function isSetId($userId): ?int
    {
        return isset($userId) ? (int) $userId : null;
    }
}

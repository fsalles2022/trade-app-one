<?php


namespace TradeAppOne\Domain\Importables;

use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;

class UserImportableDelete implements ImportableInterface
{
    protected $userService;
    public $businessRules;

    public function __construct(UserService $userService, BusinessRules $businessRules)
    {
        $this->userService   = $userService;
        $this->businessRules = $businessRules;
    }

    public function getExample(User $user = null): array
    {
        return [
            $user->cpf ?? '10816182051'
        ];
    }

    public function getColumns(): array
    {
        return [
            'cpf' => 'cpf'
        ];
    }

    public function processLine($line)
    {
        $cpf  = BrazilianDocuments::validateCpf($line['cpf']);
        $user = $this->userService->findOneByCpf($cpf);

        $this->validation($user);

        return $this->deleteUser($user);
    }

    private function deleteUser(User $user): bool
    {
        return $user->update([
            'activationStatusCode' => UserStatus::INACTIVE
        ]);
    }

    private function validation($user): void
    {
        $this->businessRules->user()
            ->hasAuthorizationUnderUser($user);
    }

    public function getType()
    {
        return Importables::USERS_DELETE;
    }
}

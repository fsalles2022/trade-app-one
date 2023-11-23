<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Importables;

use ClaroBR\Enumerators\ClaroDistributionOperations;
use ClaroBR\Rules\PhoneRule;
use ClaroBR\Services\SivAutomaticRegistrationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\BrazilianDocuments;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Components\Helpers\StringHelper;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;

class PasswordMassUpdateImportable implements ImportableInterface
{
    /** @var UserService */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /** @return string[] */
    public function getExample(): array
    {
        return [
            '13263506000305',
        ];
    }

    /** @return string[] */
    public function getColumns(): array
    {
        return [
            'cpf' => 'CPF do Usuário',
        ];
    }

    public function processLine($line): void
    {
        $this->validateLine($line);

        $user = $this->findUser($line['cpf']);

        $this->userService->resetUserPasswordUsingCpfByUser($user);
    }

    /** @throws InvalidArgumentException */
    private function findUser(string $cpf): User
    {
        $user = $this->userService->findBy($cpf);

        // Level 1 equals admin
        if (is_null($user) || $user->role->level === 1) {
            throw new \InvalidArgumentException(trans('exceptions.user.not_found'));
        }

        return $user;
    }

    /**
     * @throws InvalidArgumentException
     * @param mixed[] $data
     * @return mixed[]
     */
    private function validateLine(array $data): array
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new \InvalidArgumentException(
                $validator->errors()->first()
            );
        }

        return $data;
    }

    /** @return string[] */
    private function rules(): array
    {
        return [
            'cpf' => 'required|string|size:11',
        ];
    }

    public function getType(): string
    {
        return Importables::PASSWORD_MASS_UPDATE;
    }

    public static function buildExample(): Writer
    {
        /** @var PasswordMassUpdateImportable $passwordMassUpdateImportable */
        $passwordMassUpdateImportable = resolve(__CLASS__);
        return CsvHelper::arrayToCsv(
            [
                $passwordMassUpdateImportable->getColumns(),
                $passwordMassUpdateImportable->getExample()
            ]
        );
    }
}

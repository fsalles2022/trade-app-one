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
use TradeAppOne\Domain\Rules\Business\BusinessRules;
use TradeAppOne\Domain\Services\UserService;

class AutomaticRegistrationImportable implements ImportableInterface
{
    protected $userService;
    public $businessRules;

    protected $permission;
    protected $authenticatedUserNetwork;

    protected $sivAutomaticRegistrationService;

    private const NETWORKS_MATRICULATION = [
        NetworkEnum::VIA_VAREJO,
        NetworkEnum::GPA,
        NetworkEnum::EXTRA,
        NetworkEnum::RIACHUELO,
        NetworkEnum::TRADE_APP_ONE,
    ];

    public function __construct(
        UserService $userService,
        BusinessRules $businessRules,
        SivAutomaticRegistrationService $sivAutomaticRegistrationService
    ) {
        $this->userService                     = $userService;
        $this->businessRules                   = $businessRules;
        $this->sivAutomaticRegistrationService = $sivAutomaticRegistrationService;
        $this->authenticatedUserNetwork        = $this->userService
            ->getAuthenticatedUser()
            ->getNetwork();
    }

    public function getExample(): array
    {
        $example = [
            '13263506000305',
            'OUT ou ESTRUTURAL',
            'VENDEDOR',
            'Joao da Silva',
            '10816182051',
            'vendedor@email.com',
            '11983219999',
            'ativo',
            '04/01/1996',
            '06465134',
            'SP',
            'Barueri',
            'Alphaville',
            'Rua Bonnard',
            '980',
            'Bloco 26'
        ];

        if ($this->networksShouldBeUseMatriculation()) {
            $example['matriculation'] = '123456';
        }

        return $example;
    }

    public function getColumns(): array
    {
        $pdv = [
            'pdvCnpj' => 'pdvCnpj'
        ];

        $centralize = [
            'centralizadorOperacao' => 'centralizadorOperacao'
        ];

        $user_info = [
            'usuarioPerfil'    => 'usuarioPerfil',
            'usuarioNome'  => 'usuarioNome',
            'usuarioCpf'     => 'usuarioCpf',
            'usuarioEmail'       => 'usuarioEmail',
            'usuarioTelefone'  => 'usuarioTelefone',
            'usuarioStatus'      => 'usuarioStatus',
            'usuarioDataNascimento'      => 'usuarioDataNascimento',
            'usuarioEnderecoCep' => 'usuarioEnderecoCep',
            'usuarioEnderecoUf' => 'usuarioEnderecoUf',
            'usuarioEnderecoCidade' => 'usuarioEnderecoCidade',
            'usuarioEnderecoBairro' => 'usuarioEnderecoBairro',
            'usuarioEnderecoRua' => 'usuarioEnderecoRua',
            'usuarioEnderecoNumero' => 'usuarioEnderecoNumero',
            'usuarioEnderecoComplemento' => 'usuarioEnderecoComplemento',
        ];

        $columns = collect()
            ->merge($pdv)
            ->merge($centralize)
            ->merge($user_info)
            ->toArray();

        if ($this->networksShouldBeUseMatriculation()) {
            $columns['matriculation'] = trans('importables.user.matriculation');
        }

        return $columns;
    }

    public function networksShouldBeUseMatriculation(): bool
    {
        $pointOfSale = Auth::user()->pointsOfSale()->first();

        if (! $pointOfSale instanceof PointOfSale) {
            return false;
        }

        $network = $pointOfSale->network->slug;

        return in_array($network, self::NETWORKS_MATRICULATION, true);
    }

    public function processLine($line): void
    {
        $this->validateLine($line);
        $line_transformed = $this->transformLine($line);
        $this->sendSivAutomaticRegistration($line_transformed);
    }

    /** @param mixed[] $data */
    private function sendSivAutomaticRegistration(array $data): void
    {
        $this->sivAutomaticRegistrationService->automaticRegistration($data);
    }

    private function validateLine(array $data): array
    {
        $validator = Validator::make($data, $this->lineValidationRules());

        if ($validator->fails()) {
            throw new \InvalidArgumentException(
                $validator->errors()->first()
            );
        }

        return $data;
    }

    private function lineValidationRules(): array
    {
        return collect($this->userLineRules())
            ->merge($this->pdvLineRules())
            ->merge($this->centralizerLineRules())
            ->merge($this->matriculationRule())
            ->toArray();
    }

    private function userLineRules(): array
    {
        return [
            'usuarioPerfil'    => 'required|string',
            'usuarioNome'  => 'required|string',
            'usuarioCpf'     => 'required|string|size:11',
            'usuarioEmail'       => 'required|string|email',
            'usuarioTelefone'  => ['required', new PhoneRule],
            'usuarioStatus'      => 'required|string',
            'usuarioDataNascimento'      => 'required|date_format:d/m/Y',
            'usuarioEnderecoCep' => 'required|string|size:8',
            'usuarioEnderecoUf' => 'required|string|size:2',
            'usuarioEnderecoCidade' => 'required|string',
            'usuarioEnderecoBairro' => 'required|string',
            'usuarioEnderecoRua' => 'required|string',
            'usuarioEnderecoNumero' => 'required|string',
            'usuarioEnderecoComplemento' => 'nullable|string',
        ];
    }

    private function pdvLineRules(): array
    {
        return [
            'pdvCnpj' => 'required|string|size:14',
        ];
    }

    private function centralizerLineRules(): array
    {
        return [
            'centralizadorOperacao' => [
                'sometimes',
                'string',
                Rule::in([
                    ClaroDistributionOperations::OUT,
                    ClaroDistributionOperations::STRUCTURAL,
                ]),
            ]
        ];
    }

    public function matriculationRule()
    {
        return [
            'matriculation' => ['nullable', 'string']
        ];
    }

    /**
     *
     * @param mixed[] $line
     * @return mixed[]
     */
    private function transformLine(array $line): array
    {
        return [
            'usuario'       => $this->setUserData($line),
            'pdv'           => $this->setPdvData($line),
            'centralizador' => $this->setCentralizadorData($line),
            'matriculation' => $this->setMatriculation($line)
        ];
    }

    /**
     *
     * @param mixed[] $userInfo
     * @return mixed[]
     */
    private function setUserData(array $userInfo): array
    {
        $userName = data_get($userInfo, 'usuarioNome');
        $userCpf  = data_get($userInfo, 'usuarioCpf');

        return [
            'perfil'                      => data_get($userInfo, 'usuarioPerfil'),
            'nome'                        => strtoupper(StringHelper::removeSpecialcharactersAndAccent($userName)),
            'cpf'                         => BrazilianDocuments::validateCpf($userCpf),
            'email'                       => data_get($userInfo, 'usuarioEmail'),
            'telefone'                    => data_get($userInfo, 'usuarioTelefone'),
            'status'                      => data_get($userInfo, 'usuarioStatus'),
            'data_nascimento'             => data_get($userInfo, 'usuarioDataNascimento'),
            'endereco'  => [
                'cep'                     => data_get($userInfo, 'usuarioEnderecoCep'),
                'uf'                      => data_get($userInfo, 'usuarioEnderecoUf'),
                'cidade'                  => data_get($userInfo, 'usuarioEnderecoCidade'),
                'bairro'                  => data_get($userInfo, 'usuarioEnderecoBairro'),
                'rua'                     => data_get($userInfo, 'usuarioEnderecoRua'),
                'numero'                  => data_get($userInfo, 'usuarioEnderecoNumero'),
                'completo'                => data_get($userInfo, 'usuarioEnderecoComplemento'),
            ]
        ];
    }

    /**
     *
     * @param mixed[] $networkInfo
     * @return string
     */
    private function setMatriculation(array $matriculation): string
    {
        return data_get($matriculation, 'matriculation');
    }

    /**
     *
     * @param mixed[] $pdvInfo
     * @return mixed[]
     */
    private function setPdvData(array $pdvInfo): array
    {
        return [
            'cnpj'   => data_get($pdvInfo, 'pdvCnpj')
        ];
    }

    /**
     *
     * @param mixed[] $centralizadorInfo
     * @return mixed[]
     */
    private function setCentralizadorData(array $centralizadorInfo): array
    {
        return [
            'operacao' => data_get($centralizadorInfo, 'centralizadorOperacao')
        ];
    }

    public function getType(): string
    {
        return Importables::AUTOMATIC_REGISTRATION;
    }

    public static function buildExample(): Writer
    {
        $singleRegistrationImportable = resolve(__CLASS__);
        return CsvHelper::arrayToCsv(
            [
                $singleRegistrationImportable->getColumns(),
                $singleRegistrationImportable->getExample()
            ]
        );
    }
}

<?php

namespace TradeAppOne\Domain\Exportables;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class UserExport
{
    protected $writer;

    public function __construct()
    {
        $this->writer = CsvHelper::newFromString();
        $this->writer->insertOne($this->headings());
    }

    public function processCollection(Collection $users)
    {
        foreach ($users as $user) {
            $this->writer->insertOne($this->adapter($user));
        }
    }

    public function getCsv(): string
    {
        return $this->writer->getContent();
    }

    public function headings(): array
    {
        return [
            'primeiroNome',
            'sobrenome',
            'CPF',
            'Nascimento',
            'UltimoAcesso',
            'UsuarioDesativado',
            'SlugFuncao',
            'CargoUsuario',
            'PDV',
            'CNPJPDV',
            'ResetSenha',
            'Matricula'
        ];
    }

    public function adapter($users): array
    {
        $statusCode = data_get($users, 'activationStatusCode');
        if (Lang::has('constants.user.status.' . $statusCode)) {
            $statusCode = trans('constants.user.status.' . $statusCode);
        }

        if (Lang::has('constants.reset.status.' . $statusCode)) {
            $statusCode = trans('constants.user.status.' . $statusCode);
        }

        $last = data_get($users, 'lastSignin');

        $adapter  = [];
        $hasReset = $users->passwordResets->sortByDesc('updatedAt')->first();

        foreach ($users->pointsOfSale as $pointsOfSale) {
            $adapter += [
                $users->firstName,
                $users->lastName,
                $users->cpf,
                $users->birthday,
                Carbon::parse($last)->format('d/m/Y H:m'),
                $statusCode,
                $users->role->slug,
                $users->role->name,
                $pointsOfSale->slug,
                $pointsOfSale->cnpj,
                data_get($hasReset, 'status', trans('constants.reset.status.none')),
                $users->userAuthAlternate->document ?? null
            ];
        }

        return $adapter;
    }
}

<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class UserImportableBuilder
{
    private $user;

    public function __construct()
    {
        Storage::fake('local');
    }

    public function withUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function withRole(Role $role)
    {
        $this->role = $role;
        return $this;
    }

    public function buildFromArray($array)
    {
        $filePath = '/tmp/randomstring.csv';
        $csv = Writer::createFromString("nome;sobrenome;email;cpf;dataDeNascimento;funcao;pontoDeVenda;regional\n");

        $this->user = $this->user ?? (new UserBuilder())->build();

        $csv->setDelimiter(";");
        $csv->insertAll($array);

        file_put_contents($filePath, $csv->getContent());

        return new UploadedFile($filePath, 'test.csv', 'application/csv', null, null, true);

    }

    public function buildInvalidFile()
    {
        $filePath = '/tmp/randomstring.csv';
        file_put_contents($filePath, "HeaderA,HeaderB,HeaderC\n");

        return new UploadedFile($filePath, 'test.csv', 'application/octet-stream', null, null, true);
    }
}
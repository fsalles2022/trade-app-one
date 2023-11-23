<?php
namespace TradeAppOne\Exports\Operators\Queries\Tim;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Exports\Operators\Traits\OperatorsFilter;
use TradeAppOne\Facades\ZipFiles;

class TimUsers
{
    use OperatorsFilter;

    public const ZIP_NAME = 'tim_users.zip';
    private $folderName;
    private $zipFile;
    private $folderDir;
    public function __construct()
    {
        $this->folderName = uniqid('', true);
        $this->folderDir  = storage_path('app/'.self::ZIP_NAME);
        $this->zipFile    = ZipFiles::create($this->folderDir);
    }

    public function export($parameters): string
    {
        $this->resumeToExport($parameters)
            ->chunk(1000, function ($users, $page) {
                $csv = self::createCsv($users);
                $this->saveCsv($csv, $page);
            });
        return $this->createZip();
    }

    private static function header(): array
    {
        return [
            'CPF',
            'CustCode',
            'Primeiro nome',
            'Ultimo Nome',
            'Data de nascimento (DD/MM/YYYY)',
            'Perfil',
            'Matricula URA'
        ];
    }

    private static function collection($users): array
    {
        $adapter = [];
        foreach ($users as $user) {
            foreach ($user->pointsOfSale as $pointOfSale) {
                $adapter[] = [
                    $user->cpf,
                    data_get($pointOfSale, 'providerIdentifiers.TIM'),
                    $user->firstName,
                    $user->lastName,
                    '20/12/1990',
                    'VNBVEVA1',
                    ''
                ];
            }
        }
        return $adapter;
    }

    private static function createCsv($collection): Writer
    {
        $lines   = [];
        $lines[] = self::header();
        $lines   = array_merge($lines, self::collection($collection));
        return CsvHelper::arrayToCsv($lines);
    }

    private function saveCsv($csv, int $page): void
    {
        $filename = "$this->folderName/tim_users_$page.csv";
        Storage::disk('local')->put($filename, $csv);
        $this->zipFile->addFile($this->getPath($filename), $filename);
    }

    private function createZip(): string
    {
        ZipFiles::close($this->zipFile);
        $this->deleteCsv();
        return $this->folderDir;
    }

    private function deleteCsv(): bool
    {
        return Storage::disk('local')->deleteDirectory($this->folderName);
    }

    private function getPath(string $dir)
    {
        return Storage::disk('local')->path($dir);
    }

    public function resumeToExport($parameters): Builder
    {
        return $this->filter($parameters, Operations::TIM)
            ->with('pointsOfSale:providerIdentifiers,cnpj,slug,companyName,networkId', 'pointsOfSale.network:slug,id');
    }
}

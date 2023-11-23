<?php

namespace FastShop\Console\Commands;

use ClaroBR\Connection\SivConnection;
use FastShop\Importables\ClaroPlansImport;
use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\User;

class ProductPlansSync extends Command
{
    protected $signature   = 'products:sync {--operator=}';
    protected $description = 'Get Plans from Operators to TradeAppOne Products Table';

    public function handle(): void
    {
        $operator = $this->option('operator') ?? Operations::CLARO;
        $count    = 0;

        switch ($operator) {
            case Operations::CLARO:
                $count = $this->importPlansFromClaro();
                break;
        }

        $this->output->success(
            sprintf(
                'Importação concluída, %s planos importados e/ou atualizados. Operadora [%s]',
                $count,
                $operator
            )
        );
    }

    private function importPlansFromClaro(): int
    {
        $sentinel = config('integrations.siv.sentinel');
        $mockUser = new User(['cpf' => $sentinel]);

        $sivConnection     = resolve(SivConnection::class);
        $responsePlans     = $sivConnection->plans([], $mockUser);
        $plansFromOperator = collect(data_get($responsePlans->toArray(), 'data.data', []));

        return (new ClaroPlansImport())
            ->prepare($plansFromOperator)
            ->import();
    }
}
